{* Bisup Proxmox VE for WHMCS - Client Area Template *}

<style>
.pve-action-icon {
	display: inline-block !important;
	width: 16px;
	margin-right: 8px;
	font-size: 13px !important;
	line-height: 1 !important;
	text-align: center;
	vertical-align: -1px;
	color: #0b83a5;
}
.pve-action-icon + span {
	display: inline;
	font-size: 13px;
	font-weight: 500;
	line-height: 1.35;
	vertical-align: middle;
}
.panel-sidebar .list-group-item .pve-action-icon,
.sidebar .list-group-item .pve-action-icon,
.primary-content .list-group-item .pve-action-icon {
	margin-top: 0;
}
.panel-sidebar .list-group-item,
.sidebar .list-group-item {
	overflow-wrap: anywhere;
}
.pve-client-area {
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
	color: #263241;
	max-width: 100%;
	overflow-x: hidden;
	background: #fff;
	border: 1px solid #dfe7ef;
	border-radius: 8px;
	box-shadow: 0 14px 34px rgba(15, 32, 48, 0.08);
	padding: 18px;
}
.pve-header-panel {
	background: linear-gradient(180deg, #f8fbfd 0%, #f3f8fb 100%);
	border: 1px solid #d9e2ec;
	border-radius: 8px;
	padding: 18px;
	margin-bottom: 18px;
}
.pve-status-section {
	display: flex;
	align-items: center;
	gap: 20px;
	flex-wrap: wrap;
}
.pve-vm-icons {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 8px;
	flex-shrink: 0;
	min-width: 62px;
}
.pve-vm-icons img {
	max-width: 56px;
	height: auto;
}
.pve-status-badge {
	text-align: center;
	padding: 14px 18px;
	background: #fff;
	border: 1px solid #d9e2ec;
	border-radius: 8px;
	min-width: 95px;
	flex-shrink: 0;
	box-shadow: 0 6px 18px rgba(15, 32, 48, 0.05);
}
.pve-status-badge img {
	max-width: 44px;
	margin-bottom: 7px;
}
.pve-status-badge .status-text {
	display: block;
	text-transform: uppercase;
	font-weight: 700;
	font-size: 13px;
	color: #12304f;
	margin-bottom: 3px;
}
.pve-status-badge .uptime-text {
	font-size: 12px;
	color: #555;
}
.pve-gauges {
	display: grid;
	grid-template-columns: repeat(4, minmax(74px, 1fr));
	gap: 10px;
	flex: 1;
	min-width: 300px;
}
.pve-console-card {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 14px;
	width: 100%;
	margin-top: 16px;
	padding: 14px 16px;
	border: 1px solid #ffe0cc;
	border-radius: 8px;
	background: linear-gradient(90deg, #fff7f2 0%, #f6f3ff 100%);
	box-shadow: 0 8px 22px rgba(60, 16, 140, 0.07);
}
.pve-console-copy strong {
	display: block;
	color: #2d087b;
	font-size: 14px;
	font-weight: 700;
	line-height: 1.3;
}
.pve-console-copy span {
	display: block;
	margin-top: 3px;
	color: #6b5f7b;
	font-size: 12px;
	line-height: 1.4;
}
.pve-console-button {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	min-height: 38px;
	padding: 0 16px;
	border: 0;
	border-radius: 8px;
	background: #6354f4;
	color: #fff;
	font-size: 13px;
	font-weight: 700;
	line-height: 1;
	white-space: nowrap;
	box-shadow: 0 10px 22px rgba(99, 84, 244, 0.28);
	transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
}
.pve-console-button:hover,
.pve-console-button:focus {
	background: #4e3bdd;
	color: #fff;
	text-decoration: none;
	box-shadow: 0 12px 26px rgba(99, 84, 244, 0.34);
}
.pve-console-button:active {
	transform: translateY(1px);
}
.pve-gauge-item {
	background: #fff;
	border: 1px solid #d9e2ec;
	border-radius: 8px;
	padding: 12px;
	text-align: center;
	min-width: 0;
	box-shadow: 0 6px 18px rgba(15, 32, 48, 0.05);
}
.pve-gauge-value {
	width: 68px;
	height: 68px;
	border-radius: 50%;
	margin: 0 auto;
	display: flex;
	align-items: center;
	justify-content: center;
	background: conic-gradient(#0b83a5 0deg, #e6edf3 0deg);
	color: #12304f;
	font-weight: 700;
	font-size: 13px;
	position: relative;
}
.pve-gauge-value:before {
	content: "";
	position: absolute;
	inset: 8px;
	border-radius: 50%;
	background: #fff;
}
.pve-gauge-value span {
	position: relative;
	z-index: 1;
}
.pve-gauge-item strong {
	display: block;
	margin-top: 7px;
	font-size: 12px;
	color: #3a4a5a;
	font-weight: 600;
}
.pve-specs-table {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0;
	border: 1px solid #d9e2ec;
	border-radius: 8px;
	overflow: hidden;
	margin-bottom: 18px;
	background: #fff;
	font-size: 14px;
	table-layout: fixed;
	box-shadow: 0 8px 22px rgba(15, 32, 48, 0.05);
}
.pve-specs-table tr:not(:last-child) {
	border-bottom: 1px solid #eee;
}
.pve-specs-table td {
	padding: 13px 16px;
	vertical-align: top;
	font-size: 14px;
	line-height: 1.5;
	overflow-wrap: anywhere;
	word-break: break-word;
}
.pve-specs-table td:first-child {
	background: #f8fafc;
	width: 180px;
	font-weight: 600;
	color: #444;
	border-right: 1px solid #d9e2ec;
}
.pve-specs-table .spec-label {
	font-weight: 600;
	font-size: 14px;
	color: #12304f;
}
.pve-specs-table .spec-sublabel {
	font-size: 12px;
	color: #555;
	font-weight: normal;
}
.spec-value {
	font-weight: 600;
	font-size: 14px;
	color: #333;
}
.spec-detail {
	font-size: 14px;
	color: #555;
	margin-top: 3px;
}
.pve-specs-table code {
	background: #eef6f8;
	padding: 2px 6px;
	border-radius: 3px;
	font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
	font-size: 13px;
	color: #0c6674;
	white-space: normal;
	overflow-wrap: anywhere;
}
.pve-loading-line {
	display: inline-block;
	height: 14px;
	min-width: 120px;
	border-radius: 999px;
	background: linear-gradient(90deg, #edf3f8 0%, #f8fbfd 45%, #edf3f8 90%);
	background-size: 220% 100%;
	animation: pveSkeleton 1.2s ease-in-out infinite;
	vertical-align: middle;
}
.pve-loading-line.short {
	min-width: 64px;
}
.pve-loading-line.medium {
	min-width: 180px;
}
.pve-loading-line.long {
	min-width: 280px;
	max-width: 100%;
}
.pve-live-note {
	margin: 0 0 12px;
	padding: 10px 12px;
	border: 1px solid #d9e2ec;
	border-radius: 6px;
	background: #f8fbfd;
	color: #456;
	font-size: 13px;
}
.pve-alert-warning {
	background: #fff3cd;
	border: 1px solid #ffc107;
	border-radius: 6px;
	padding: 15px;
	color: #856404;
	font-size: 13px;
}
.pve-stats-section {
	margin-top: 25px;
}
.pve-stats-section h4 {
	color: #12304f;
	font-weight: 600;
	margin-bottom: 15px;
	padding-bottom: 10px;
	border-bottom: 2px solid #00a7bd;
}
.pve-stats-tabs {
	display: flex;
	gap: 5px;
	flex-wrap: wrap;
	margin-bottom: 0;
	padding: 0;
	list-style: none;
	border-bottom: 1px solid #ddd;
}
.pve-stats-tabs li {
	margin: 0;
}
.pve-stats-tabs li a {
	display: block;
	padding: 10px 20px;
	text-decoration: none;
	color: #666;
	background: #f5f5f5;
	border: 1px solid #ddd;
	border-bottom: none;
	border-radius: 6px 6px 0 0;
	font-weight: 500;
	font-size: 13px;
	transition: all 0.2s ease;
}
.pve-stats-tabs li.active a {
	background: #fff;
	color: #12304f;
	border-color: #ddd;
}
.pve-stats-content {
	background: #fff;
	border: 1px solid #ddd;
	border-top: none;
	border-radius: 0 0 8px 8px;
	padding: 20px;
}
.pve-stats-content .tab-pane {
	display: none;
}
.pve-stats-content .tab-pane.active {
	display: block;
}
.pve-graphs-grid {
	display: flex;
	flex-direction: column;
	gap: 15px;
}
.pve-graphs-grid img {
	width: 100%;
	height: auto;
	border-radius: 6px;
	border: 1px solid #eee;
}
@keyframes pveSkeleton {
	0% { background-position: 120% 0; }
	100% { background-position: -120% 0; }
}
@media (max-width: 768px) {
	.pve-status-section {
		flex-direction: column;
		text-align: center;
		gap: 15px;
	}
	.pve-vm-icons {
		flex-direction: row;
		gap: 15px;
	}
	.pve-gauges {
		grid-template-columns: repeat(2, minmax(0, 1fr));
		width: 100%;
		min-width: 0;
	}
	.pve-console-card {
		align-items: stretch;
		flex-direction: column;
	}
	.pve-console-button {
		width: 100%;
	}
	.pve-specs-table,
	.pve-specs-table tbody,
	.pve-specs-table tr,
	.pve-specs-table td {
		display: block;
		width: 100%;
	}
	.pve-specs-table tr:not(:last-child) {
		border-bottom: 1px solid #d9e2ec;
	}
	.pve-specs-table td:first-child {
		width: 100%;
		border-right: 0;
		border-bottom: 1px solid #eef2f6;
		padding-bottom: 8px;
	}
	.pve-specs-table td:last-child {
		padding-top: 10px;
	}
}
</style>

<div class="pve-client-area">
	{if $load_error}
		<div class="pve-alert-warning">{$load_error}</div>
	{else}
		<p class="pve-live-note" id="pve-live-note" style="display:none;"></p>

		<div class="pve-header-panel">
			<div class="pve-status-section">
				<div class="pve-vm-icons">
					<img id="pve-vtype-icon" src="./modules/servers/pvewhmcs/img/{$vm_config['vtype']|default:'qemu'}.png" alt="{$vm_config['vtype']|default:'qemu'}"/>
					<img id="pve-os-icon" src="./modules/servers/pvewhmcs/img/os/other.png" alt="OS"/>
				</div>

				<div class="pve-status-badge">
					<img id="pve-status-icon" src="./modules/servers/pvewhmcs/img/stopped.png" alt="status"/>
					<span class="status-text" id="pve-status-text"><span class="pve-loading-line short"></span></span>
					<span class="uptime-text" id="pve-uptime-text">Live status pending</span>
				</div>

				<div class="pve-gauges">
					<div class="pve-gauge-item">
						<div class="pve-gauge-value" id="pve-cpu-gauge"><span>--</span></div>
						<strong>CPU</strong>
					</div>
					<div class="pve-gauge-item">
						<div class="pve-gauge-value" id="pve-ram-gauge"><span>--</span></div>
						<strong>RAM</strong>
					</div>
					<div class="pve-gauge-item">
						<div class="pve-gauge-value" id="pve-disk-gauge"><span>--</span></div>
						<strong>Disk</strong>
					</div>
					<div class="pve-gauge-item">
						<div class="pve-gauge-value" id="pve-swap-gauge"><span>--</span></div>
						<strong>Swap</strong>
					</div>
				</div>
				<div class="pve-console-card">
					<div class="pve-console-copy">
						<strong>VPS web console</strong>
						<span>Open a secure noVNC session in a dedicated console window.</span>
					</div>
					<button type="button" class="pve-console-button" data-pve-novnc-launch>
						<i class="fa fa-desktop"></i>
						<span>Open console</span>
					</button>
				</div>
			</div>
		</div>

		<table class="pve-specs-table">
			<tr>
				<td><span class="spec-label">Memory</span> <span class="spec-sublabel">(RAM)</span></td>
				<td><span class="spec-value" data-pve-field="memory"><span class="pve-loading-line short"></span></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">Compute</span> <span class="spec-sublabel">(CPU)</span></td>
				<td data-pve-field="compute"><span class="pve-loading-line medium"></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">Storage</span> <span class="spec-sublabel">(SSD/HDD)</span></td>
				<td data-pve-field="storage"><span class="pve-loading-line medium"></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">Boot Order</span></td>
				<td><span class="spec-value" data-pve-field="boot"><span class="pve-loading-line short"></span></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">IPv4</span> <span class="spec-sublabel">(Networking)</span></td>
				<td>
					<span class="spec-value">{$vm_config['ipv4']|default:'-'}</span>
					<div class="spec-detail">Mask: {$vm_config['netmask4']|default:'-'} &bull; Gateway: {$vm_config['gateway4']|default:'-'}</div>
				</td>
			</tr>
			<tr>
				<td><span class="spec-label">IP Config</span> <span class="spec-sublabel">(IPv4/v6)</span></td>
				<td data-pve-field="ipconfig"><span class="pve-loading-line long"></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">NIC #0</span> <span class="spec-sublabel">(Primary)</span></td>
				<td data-pve-field="net0"><span class="pve-loading-line long"></span></td>
			</tr>
			<tr id="pve-net1-row" style="display:none;">
				<td><span class="spec-label">NIC #1</span> <span class="spec-sublabel">(Secondary)</span></td>
				<td data-pve-field="net1"></td>
			</tr>
			<tr>
				<td><span class="spec-label">Config</span> <span class="spec-sublabel">(Tweaks)</span></td>
				<td data-pve-field="config"><span class="pve-loading-line short"></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">Kernel</span> <span class="spec-sublabel">(OS)</span></td>
				<td><span class="spec-value" data-pve-field="kernel"><span class="pve-loading-line short"></span></span></td>
			</tr>
			<tr>
				<td><span class="spec-label">Node</span></td>
				<td><span class="spec-value" data-pve-field="node">{$vm_config['node_name']|default:'Resolving...'}</span></td>
			</tr>
		</table>

		{if $load_stats}
		<div class="pve-stats-section">
			<h4><i class="fa fa-line-chart"></i> Guest Statistics</h4>
			<div id="pve-stats-loading" class="pve-alert-warning">Loading graph data from Proxmox...</div>
			<div id="pve-stats-wrap" style="display:none;">
				<ul class="pve-stats-tabs" role="tablist">
					<li class="active"><a data-toggle="tab" role="tab" href="#dailystat">Daily</a></li>
					<li><a data-toggle="tab" role="tab" href="#weeklystat">Weekly</a></li>
					<li><a data-toggle="tab" role="tab" href="#monthlystat">Monthly</a></li>
					<li><a data-toggle="tab" role="tab" href="#yearlystat">Yearly</a></li>
				</ul>
				<div class="pve-stats-content tab-content">
					<div id="dailystat" class="tab-pane active"><div class="pve-graphs-grid" data-pve-stats="day"></div></div>
					<div id="weeklystat" class="tab-pane"><div class="pve-graphs-grid" data-pve-stats="week"></div></div>
					<div id="monthlystat" class="tab-pane"><div class="pve-graphs-grid" data-pve-stats="month"></div></div>
					<div id="yearlystat" class="tab-pane"><div class="pve-graphs-grid" data-pve-stats="year"></div></div>
				</div>
			</div>
		</div>
		{/if}
	{/if}
</div>

{if !$load_error}
<script>
window.pveWhmcsManage = {
	serviceId: "{$params['serviceid']|escape:'javascript'}",
	token: "{$ajax_token|escape:'javascript'}",
	loadStats: {if $load_stats}true{else}false{/if}
};
</script>
{literal}
<script>
(function ($) {
	function text(value, fallback) {
		return value === undefined || value === null || value === '' ? (fallback || '-') : value;
	}

	function htmlEscape(value) {
		return $('<div>').text(text(value)).html();
	}

	function setField(name, value) {
		$('[data-pve-field="' + name + '"]').html(value);
	}

	function setGauge(id, percent) {
		var clean = Math.max(0, Math.min(100, parseFloat(percent) || 0));
		var deg = clean * 3.6;
		$(id).css('background', 'conic-gradient(#0b83a5 ' + deg + 'deg, #e6edf3 ' + deg + 'deg)').find('span').text(clean + '%');
	}

	function parts(value) {
		if (!value) {
			return [];
		}
		return String(value).split(',');
	}

	function sizeFromDisk(value) {
		var found = parts(value).filter(function (part) {
			return part.indexOf('size=') === 0;
			});
		return found.length ? found[0].replace('size=', '') : '';
	}

	function formatConfigLine(value) {
		return htmlEscape(value).replace(/,/g, ' &bull; ').replace(/=/g, ': ');
	}

	function formatNet(value) {
		if (!value) {
			return '-';
		}
		return parts(value).map(function (part) {
			var kv = part.split('=');
			if (kv.length < 2) {
				return '<div class="spec-detail">' + htmlEscape(part) + '</div>';
			}
			var label = htmlEscape(kv[0]);
			var val = htmlEscape(kv.slice(1).join('='));
			if (['virtio', 'e1000', 'rtl8139'].indexOf(kv[0]) !== -1) {
				return '<div class="spec-detail"><strong>' + label + '</strong>: <code>' + val + '</code></div>';
			}
			return '<div class="spec-detail"><strong>' + label + '</strong>: ' + val + '</div>';
		}).join('');
	}

	function renderStorage(config) {
		var disks = [];
		['rootfs', 'ide0', 'scsi0', 'virtio0'].forEach(function (key) {
			var size = sizeFromDisk(config[key]);
			if (size) {
				disks.push('<div class="spec-detail"><span class="spec-value">' + htmlEscape(size) + '</span> (' + key + ')</div>');
			}
		});
		return disks.length ? disks.join('') : '-';
	}

	function renderStats(stats) {
		if (!stats || !stats.cpu || !stats.cpu.day) {
			$('#pve-stats-loading').text('Stats Error: RRD unavailable. Ask support to check Proxmox RRD data.');
			return;
		}
		['day', 'week', 'month', 'year'].forEach(function (period) {
			var target = $('[data-pve-stats="' + period + '"]');
			target.empty();
			[
				['CPU', stats.cpu && stats.cpu[period]],
				['Memory', stats.mem && stats.mem[period]],
				['Network I/O', stats.netinout && stats.netinout[period]],
				['Disk I/O', stats.diskrw && stats.diskrw[period]]
			].forEach(function (item) {
				if (item[1]) {
					target.append('<img src="data:image/png;base64,' + item[1] + '" alt="' + item[0] + ' (' + period + ')">');
				}
			});
		});
		$('#pve-stats-loading').hide();
		$('#pve-stats-wrap').show();
	}

	function hydrate(payload) {
		var config = payload.config || {};
		var status = payload.status || {};
		var ostype = text(config.ostype, 'other');

		$('#pve-live-note').hide().text('');
		$('#pve-vtype-icon').attr('src', './modules/servers/pvewhmcs/img/' + text(config.vtype, 'qemu') + '.png');
		$('#pve-os-icon').attr('src', './modules/servers/pvewhmcs/img/os/' + ostype + '.png').attr('alt', ostype);
		$('#pve-status-icon').attr('src', './modules/servers/pvewhmcs/img/' + text(status.status, 'stopped') + '.png');
		$('#pve-status-text').text(text(status.status, 'unknown'));
		$('#pve-uptime-text').text(status.uptime ? 'Up ' + status.uptime : 'Uptime unavailable');

		setGauge('#pve-cpu-gauge', status.cpu);
		setGauge('#pve-ram-gauge', status.memusepercent);
		setGauge('#pve-disk-gauge', status.diskusepercent);
		setGauge('#pve-swap-gauge', status.swapusepercent);

		setField('memory', htmlEscape(text(config.memory, '-')) + (config.memory ? 'MB' : ''));
		setField('compute', '<span class="spec-value">' + htmlEscape(text(config.cores, '-')) + ' core(s)</span><div class="spec-detail">on ' + htmlEscape(text(config.sockets, '-')) + ' socket(s)</div>');
		setField('storage', renderStorage(config));
		setField('boot', htmlEscape(text(config.boot, '-')).replace('order=', '').replace(/;/g, ' -> '));
		setField('ipconfig', [
			config.ipconfig0 ? '<div class="spec-detail"><strong>NIC #0:</strong> ' + formatConfigLine(config.ipconfig0) + '</div>' : '',
			config.ipconfig1 ? '<div class="spec-detail"><strong>NIC #1:</strong> ' + formatConfigLine(config.ipconfig1) + '</div>' : ''
		].join('') || '-');
		setField('net0', formatNet(config.net0));
		if (config.net1) {
			$('#pve-net1-row').show();
			setField('net1', formatNet(config.net1));
		}
		setField('config', '<span class="spec-detail"><strong>On-boot?</strong> ' + (config.onboot ? 'Yes' : 'No (contact support)') + '</span>');
		setField('kernel', htmlEscape(text(config.ostype, '-')));
		setField('node', htmlEscape(text(payload.node, '-')));

		if (window.pveWhmcsManage.loadStats) {
			renderStats(payload.statistics);
		}
	}

	function launchNovnc(event) {
		if (event) {
			event.preventDefault();
			event.stopPropagation();
			if (event.stopImmediatePropagation) {
				event.stopImmediatePropagation();
			}
		}
		if (window.pveNovncLaunching) {
			return false;
		}
		window.pveNovncLaunching = true;
			var width = Math.min(1280, Math.max(960, Math.floor(screen.availWidth * 0.82)));
			var height = Math.min(860, Math.max(680, Math.floor(screen.availHeight * 0.82)));
			var left = Math.max(0, Math.floor((screen.availWidth - width) / 2));
			var top = Math.max(0, Math.floor((screen.availHeight - height) / 2));
			var features = [
				'popup=yes',
				'width=' + width,
				'height=' + height,
				'left=' + left,
				'top=' + top,
				'resizable=yes',
				'scrollbars=no'
			].join(',');
			var consoleWindow = window.open('', 'bisupVpsConsole', features);
			$('#pve-live-note').show().text('Preparing console...');

			$.ajax({
				url: './modules/servers/pvewhmcs/novnc_launch.php',
				method: 'POST',
				dataType: 'json',
				cache: false,
				data: {
					serviceid: window.pveWhmcsManage.serviceId,
					token: window.pveWhmcsManage.token
				}
			}).done(function (payload) {
				if (payload && payload.success && payload.url) {
					$('#pve-live-note').hide().text('');
					if (consoleWindow) {
						consoleWindow.location = payload.url;
					} else {
						window.location.href = payload.url;
					}
					window.pveNovncLaunching = false;
					return;
				}
				if (consoleWindow) {
					consoleWindow.close();
				}
				window.pveNovncLaunching = false;
				$('#pve-live-note').show().text(payload && payload.message ? payload.message : 'Unable to prepare console.');
			}).fail(function () {
				if (consoleWindow) {
					consoleWindow.close();
				}
				window.pveNovncLaunching = false;
				$('#pve-live-note').show().text('Unable to prepare console. Please refresh and try again.');
			});
		return false;
	}

	document.addEventListener('click', function (event) {
		var trigger = event.target.closest('[data-pve-novnc-launch], a[href*="a=noVNC"], button[name="a"][value="noVNC"], input[name="a"][value="noVNC"]');
		if (trigger) {
			launchNovnc(event);
		}
	}, true);

	$(function () {
		if (/[?&]a=noVNC\b/.test(window.location.search)) {
			var legacyRouterLink = $('a[href*="novnc_router.php?token="]').first();
			if (legacyRouterLink.length) {
				var legacyBlock = legacyRouterLink.closest('div');
				for (var i = 0; i < 5 && legacyBlock.length && legacyBlock.prop('tagName') !== 'BODY'; i++) {
					if (legacyBlock.text().indexOf('Action Failed') !== -1 || legacyBlock.text().indexOf('Console is ready') !== -1) {
						legacyBlock.hide();
					}
					legacyBlock = legacyBlock.parent();
				}
			}
			history.replaceState(null, document.title, window.location.pathname + window.location.search.replace(/([?&])modop=custom&?/, '$1').replace(/([?&])a=noVNC&?/, '$1').replace(/[?&]$/, '') + window.location.hash);
			launchNovnc();
		}

		$(document).on('click', '[data-pve-novnc-launch], a[href*="a=noVNC"]', launchNovnc);

		$.ajax({
			url: './modules/servers/pvewhmcs/clientarea_data.php',
			dataType: 'json',
			cache: false,
			data: {
				serviceid: window.pveWhmcsManage.serviceId,
				token: window.pveWhmcsManage.token,
				stats: window.pveWhmcsManage.loadStats ? '1' : '0'
			}
		}).done(function (payload) {
			if (!payload || !payload.success) {
				$('#pve-live-note').show().text(payload && payload.message ? payload.message : 'Unable to load live Proxmox details.');
				return;
			}
			hydrate(payload);
		}).fail(function () {
			$('#pve-live-note').show().text('Unable to load live Proxmox details. Try refreshing the page.');
		});
	});
})(jQuery);
</script>
{/literal}
{/if}
