#!/usr/bin/env node

const crypto = require('crypto');
const fs = require('fs');
const http = require('http');
const path = require('path');
const WebSocket = require('ws');

const listenHost = process.env.PVEWHMCS_NOVNC_HOST || '127.0.0.1';
const listenPort = parseInt(process.env.PVEWHMCS_NOVNC_PORT || '8787', 10);
const sessionDir = process.env.PVEWHMCS_NOVNC_SESSION_DIR || path.join(__dirname, 'novnc_sessions');

function sessionPath(token) {
  return path.join(sessionDir, crypto.createHash('sha256').update(String(token)).digest('hex') + '.json');
}

function extractToken(url) {
  const pathname = (url || '').split(/[?#]/)[0];
  const parts = pathname.split('/').map((part) => decodeURIComponent(part)).filter(Boolean);
  return parts.find((part) => /^[a-f0-9]{32,128}$/i.test(part)) || '';
}

function loadSession(token) {
  if (!/^[a-f0-9]{32,128}$/i.test(token || '')) {
    throw new Error('invalid token');
  }

  const file = sessionPath(token);
  const payload = JSON.parse(fs.readFileSync(file, 'utf8'));
  if (!payload.expires || payload.expires < Math.floor(Date.now() / 1000)) {
    try { fs.unlinkSync(file); } catch (_) {}
    throw new Error('expired token');
  }

  return payload;
}

function redact(message) {
  return String(message)
    .replace(/([?&]vncticket=)[^&\s]+/ig, '$1[redacted]')
    .replace(/(PVEAuthCookie=)[^;\s]+/ig, '$1[redacted]');
}

const server = http.createServer((req, res) => {
  res.writeHead(200, { 'content-type': 'text/plain' });
  res.end('Bisup noVNC proxy is running\n');
});

const wss = new WebSocket.Server({ server });

wss.on('connection', (client, req) => {
  const token = extractToken(req.url);
  let upstream;

  try {
    const session = loadSession(token);
    const target = `wss://${session.host}:${session.port}/${session.path}`;
    console.log(`[novnc] connect ${redact(target)}`);

    upstream = new WebSocket(target, {
      rejectUnauthorized: false,
      headers: {
        Cookie: `PVEAuthCookie=${session.pveticket}`,
        Origin: `https://${session.host}:${session.port}`,
      },
    });

    upstream.on('open', () => {
      client.on('message', (data, isBinary) => {
        if (upstream && upstream.readyState === WebSocket.OPEN) {
          upstream.send(data, { binary: isBinary });
        }
      });
      upstream.on('message', (data, isBinary) => {
        if (client.readyState === WebSocket.OPEN) {
          client.send(data, { binary: isBinary });
        }
      });
    });

    upstream.on('close', (code, reason) => {
      console.log(`[novnc] upstream close ${code} ${redact(reason || '')}`);
      if (client.readyState === WebSocket.OPEN) {
        client.close(code || 1011, reason ? reason.toString() : 'upstream closed');
      }
    });

    upstream.on('error', (error) => {
      console.error(`[novnc] upstream error ${redact(error.message)}`);
      if (client.readyState === WebSocket.OPEN) {
        client.close(1011, 'upstream error');
      }
    });

    client.on('close', () => {
      if (upstream && upstream.readyState === WebSocket.OPEN) {
        upstream.close();
      }
    });
  } catch (error) {
    console.error(`[novnc] reject ${redact(error.message)}`);
    client.close(1008, 'console session invalid');
  }
});

server.listen(listenPort, listenHost, () => {
  console.log(`[novnc] listening on ${listenHost}:${listenPort}`);
});
