<?php
function renderHead($title = 'SmartFinder', $extra = '') {
echo '<!DOCTYPE html><html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>'.$title.' — SmartFinder</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root{
  --primary:#4ade80;--primary-dark:#22c55e;--primary-dim:#166534;
  --primary-light:rgba(74,222,128,.12);--primary-border:rgba(74,222,128,.25);
  --accent:#fbbf24;--accent-light:rgba(251,191,36,.12);
  --bg:#1a1f2e;--bg2:#212736;--bg3:#272d3d;--bg4:#2e3547;
  --card:#232938;--card2:#2a3145;
  --border:#313a52;--border2:#3b4560;
  --text:#e2e8f4;--text2:#94a3b8;--text3:#5c6d88;
  --red:#f87171;--red-dim:rgba(248,113,113,.12);--red-border:rgba(248,113,113,.25);
  --green:#4ade80;--green-dim:rgba(74,222,128,.1);
  --blue:#60a5fa;--blue-dim:rgba(96,165,250,.12);
  --yellow:#fbbf24;--yellow-dim:rgba(251,191,36,.12);
  --radius:12px;
  --shadow:0 2px 8px rgba(0,0,0,.25);
  --shadow-md:0 4px 20px rgba(0,0,0,.35);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif;font-size:15px;min-height:100vh;}
a{color:var(--primary);text-decoration:none;}
a:hover{text-decoration:underline;}

/* TOPBAR */
.topbar{background:var(--bg2);border-bottom:1px solid var(--border);padding:0 32px;height:64px;display:flex;align-items:center;gap:16px;position:sticky;top:0;z-index:100;box-shadow:0 1px 12px rgba(0,0,0,.3);}
.brand{display:flex;align-items:center;gap:10px;font-family:"DM Serif Display",serif;font-size:20px;color:var(--text);text-decoration:none;}
.brand-icon{width:36px;height:36px;background:var(--primary-dim);border:1px solid var(--primary-border);border-radius:9px;display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:16px;}
.brand span{color:var(--primary);}
.nav-links{display:flex;gap:4px;margin-left:auto;align-items:center;}
.nav-links a{display:flex;align-items:center;gap:7px;padding:8px 14px;border-radius:8px;color:var(--text2);font-size:14px;font-weight:500;transition:all .15s;text-decoration:none;}
.nav-links a:hover{background:var(--bg3);color:var(--text);}
.nav-links a.active{background:var(--primary-light);color:var(--primary);font-weight:600;}
.nav-btn-login{background:var(--primary);color:#0f1a10 !important;border-radius:8px;padding:8px 18px !important;font-weight:700 !important;}
.nav-btn-login:hover{background:var(--primary-dark) !important;color:#0f1a10 !important;}
.nav-btn-logout{background:var(--red-dim);color:var(--red) !important;border:1px solid var(--red-border);}
.nav-btn-logout:hover{background:rgba(248,113,113,.2) !important;}

/* SIDEBAR */
.app-layout{display:flex;min-height:calc(100vh - 64px);}
.sidebar{width:230px;background:var(--bg2);border-right:1px solid var(--border);flex-shrink:0;padding:20px 12px;}
.sidebar-section{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);padding:10px 12px 4px;margin-top:6px;}
.sidebar a{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;color:var(--text2);font-size:14px;font-weight:500;text-decoration:none;transition:all .15s;margin-bottom:2px;}
.sidebar a:hover{background:var(--bg3);color:var(--text);}
.sidebar a.active{background:var(--primary-light);color:var(--primary);font-weight:600;}
.sidebar a i{width:16px;text-align:center;}
.sidebar a.danger{color:var(--red);}
.sidebar a.danger:hover{background:var(--red-dim);}
.main-content{flex:1;padding:28px 32px;min-width:0;}

/* WRAP */
.wrap{max-width:1200px;margin:0 auto;padding:32px 24px;}

/* PAGE HEADER */
.page-header{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:28px;}
.page-header h1{font-size:23px;font-weight:700;letter-spacing:-.3px;}
.page-header p{color:var(--text2);margin-top:4px;font-size:14px;}

/* CARD */
.card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:24px;box-shadow:var(--shadow);}

/* STAT CARDS */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:28px;}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);display:flex;align-items:center;gap:16px;}
.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.si-green{background:var(--green-dim);color:var(--green);}
.si-yellow{background:var(--yellow-dim);color:var(--yellow);}
.si-blue{background:var(--blue-dim);color:var(--blue);}
.si-red{background:var(--red-dim);color:var(--red);}
.si-primary{background:var(--primary-light);color:var(--primary);}
.stat-val{font-size:30px;font-weight:700;line-height:1;font-family:"DM Serif Display",serif;color:var(--text);}
.stat-label{font-size:13px;color:var(--text2);margin-top:4px;}

/* TABLE */
.table-wrap{overflow-x:auto;border-radius:var(--radius);border:1px solid var(--border);background:var(--card);box-shadow:var(--shadow);}
table{width:100%;border-collapse:collapse;font-size:14px;}
thead th{background:var(--bg3);padding:11px 16px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text3);border-bottom:1px solid var(--border);}
tbody tr{border-bottom:1px solid var(--border);transition:background .12s;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:var(--bg3);}
td{padding:12px 16px;color:var(--text);}

/* BADGES */
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:99px;font-size:11.5px;font-weight:700;}
.badge::before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor;flex-shrink:0;}
.b-unclaimed{background:rgba(251,191,36,.12);color:#fbbf24;}
.b-claimed{background:var(--green-dim);color:var(--green);}
.b-expired{background:rgba(100,116,139,.1);color:var(--text3);}
.b-valid{background:var(--green-dim);color:var(--green);}
.b-invalid{background:var(--red-dim);color:var(--red);}
.b-pending{background:rgba(251,191,36,.12);color:#fbbf24;}
.b-admin{background:var(--blue-dim);color:var(--blue);}
.b-petugas{background:var(--primary-light);color:var(--primary);}

/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;border:none;cursor:pointer;font-size:14px;font-weight:600;font-family:inherit;transition:all .15s;text-decoration:none;white-space:nowrap;line-height:1.2;}
.btn:hover{text-decoration:none;}
.btn-primary{background:var(--primary);color:#0f1a10;box-shadow:0 2px 10px rgba(74,222,128,.2);}
.btn-primary:hover{background:var(--primary-dark);transform:translateY(-1px);box-shadow:0 4px 16px rgba(74,222,128,.3);}
.btn-secondary{background:var(--bg3);color:var(--text);border:1px solid var(--border2);}
.btn-secondary:hover{background:var(--bg4);}
.btn-danger{background:var(--red-dim);color:var(--red);border:1px solid var(--red-border);}
.btn-danger:hover{background:rgba(248,113,113,.2);}
.btn-success{background:var(--green-dim);color:var(--green);border:1px solid rgba(74,222,128,.25);}
.btn-success:hover{background:rgba(74,222,128,.18);}
.btn-warning{background:var(--yellow-dim);color:var(--yellow);border:1px solid rgba(251,191,36,.25);}
.btn-warning:hover{background:rgba(251,191,36,.2);}
.btn-sm{padding:5px 12px;font-size:12px;border-radius:6px;}
.btn-xs{padding:3px 9px;font-size:11px;border-radius:5px;}
.btn-icon{width:34px;height:34px;padding:0;justify-content:center;border-radius:8px;}

/* FORMS */
.form-grid{display:grid;gap:18px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px;}
.form-group{display:flex;flex-direction:column;gap:6px;}
.form-group label{font-size:13px;font-weight:600;color:var(--text);}
.form-group .hint{font-size:12px;color:var(--text3);}
.form-control{background:var(--bg3);border:1.5px solid var(--border2);border-radius:8px;padding:10px 14px;color:var(--text);font-size:14px;font-family:inherit;transition:border-color .15s,box-shadow .15s;width:100%;}
.form-control:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(74,222,128,.15);}
select.form-control{cursor:pointer;}
textarea.form-control{min-height:90px;resize:vertical;}
.form-control::placeholder{color:var(--text3);}
.input-group{position:relative;}
.input-group i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:14px;}
.input-group .form-control{padding-left:36px;}

/* SEARCH BAR */
.search-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:20px;}
.search-wrap{position:relative;flex:1;min-width:200px;}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text3);font-size:14px;}
.search-wrap input{background:var(--bg3);border:1.5px solid var(--border2);border-radius:8px;padding:9px 14px 9px 36px;color:var(--text);font-size:14px;font-family:inherit;width:100%;}
.search-wrap input::placeholder{color:var(--text3);}
.search-wrap input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(74,222,128,.12);}

/* ALERTS */
.alert{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;border-width:1px;border-style:solid;}
.alert-success{background:var(--green-dim);border-color:rgba(74,222,128,.25);color:var(--green);}
.alert-error{background:var(--red-dim);border-color:var(--red-border);color:var(--red);}
.alert-info{background:var(--blue-dim);border-color:rgba(96,165,250,.25);color:var(--blue);}
.alert-warning{background:var(--yellow-dim);border-color:rgba(251,191,36,.25);color:var(--yellow);}

/* EMPTY STATE */
.empty-state{text-align:center;padding:60px 24px;color:var(--text2);}
.empty-state i{font-size:48px;margin-bottom:16px;color:var(--border2);display:block;}
.empty-state h3{font-size:18px;color:var(--text);margin-bottom:8px;font-weight:600;}

/* DIVIDER */
hr{border:none;border-top:1px solid var(--border);margin:24px 0;}

/* PAGINATION */
.pagination{display:flex;gap:6px;margin-top:20px;}
.pagination a,.pagination span{display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:7px;font-size:13px;font-weight:600;background:var(--bg3);border:1px solid var(--border2);color:var(--text2);text-decoration:none;transition:all .15s;}
.pagination a:hover{background:var(--primary-light);border-color:var(--primary-border);color:var(--primary);}
.pagination .pg-active{background:var(--primary);border-color:var(--primary);color:#0f1a10;}

/* FOTO */
.foto-preview{width:72px;height:52px;border-radius:8px;object-fit:cover;border:1px solid var(--border);}

/* TABS */
.tabs{display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:24px;}
.tab-item{padding:10px 20px;font-size:14px;font-weight:600;color:var(--text2);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-1px;text-decoration:none;transition:all .15s;}
.tab-item:hover{color:var(--text);}
.tab-item.active{color:var(--primary);border-bottom-color:var(--primary);}

/* RESPONSIVE */
@media(max-width:900px){.sidebar{display:none;}.main-content{padding:20px 16px;}.form-row,.form-row-3{grid-template-columns:1fr;}}
@media(max-width:600px){.wrap{padding:20px 16px;}.stats-grid{grid-template-columns:1fr 1fr;}.topbar{padding:0 16px;}}
</style>'.$extra.'
</head><body>';
}

function renderTopbar($active = '') {
    $loggedIn = isLoggedIn();
    $aHome = $active==='home'?'active':'';
    echo '<nav class="topbar">
    <a href="index.php" class="brand"><div class="brand-icon"><i class="fa-solid fa-magnifying-glass-location"></i></div>Smart<span>Finder</span></a>
    <div class="nav-links">
        <a href="index.php" class="'.$aHome.'"><i class="fa-solid fa-house"></i> Beranda</a>';
    if ($loggedIn) {
        echo '<a href="dashboard.php" class="btn btn-primary" style="padding:7px 16px;font-size:13px;"><i class="fa-solid fa-grid-2"></i> Dashboard</a>
        <a href="logout.php" class="nav-btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>';
    } else {
        echo '<a href="login.php" class="nav-btn-login"><i class="fa-solid fa-lock"></i> Login Petugas</a>';
    }
    echo '</div></nav>';
}

function renderSidebar($active = '') {
    $nama = $_SESSION['nama_petugas'] ?? 'Petugas';
    $role = $_SESSION['role'] ?? 'petugas';
    $isAdmin = $role === 'admin';
    echo '<div class="sidebar">
    <div style="padding:4px 12px 16px;border-bottom:1px solid var(--border);margin-bottom:8px;">
        <div style="font-weight:700;font-size:14px;color:var(--text);">'.htmlspecialchars($nama).'</div>
        <div style="margin-top:4px;"><span class="badge '.($isAdmin?'b-admin':'b-petugas').'">'.$role.'</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <a href="dashboard.php" class="'.($active==='dashboard'?'active':'').'"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
    <a href="kelola_barang.php" class="'.($active==='barang'?'active':'').'"><i class="fa-solid fa-boxes-stacked"></i> Data Barang</a>
    <a href="tambah_barang.php" class="'.($active==='tambah'?'active':'').'"><i class="fa-solid fa-plus-circle"></i> Tambah Barang</a>
    <a href="kelola_klaim.php" class="'.($active==='klaim'?'active':'').'"><i class="fa-solid fa-clipboard-check"></i> Kelola Klaim</a>
    <div class="sidebar-section">Laporan</div>
    <a href="laporan.php" class="'.($active==='laporan'?'active':'').'"><i class="fa-solid fa-chart-bar"></i> Laporan</a>
    <a href="export.php" class="'.($active==='export'?'active':'').'"><i class="fa-solid fa-file-export"></i> Ekspor Data</a>';
    if ($isAdmin) {
        echo '<div class="sidebar-section">Admin</div>
        <a href="kelola_user.php" class="'.($active==='user'?'active':'').'"><i class="fa-solid fa-users"></i> Manajemen User</a>
        <a href="activity_log.php" class="'.($active==='log'?'active':'').'"><i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas</a>';
    }
    echo '<div class="sidebar-section">Akun</div>
    <a href="profil.php" class="'.($active==='profil'?'active':'').'"><i class="fa-solid fa-user-circle"></i> Profil Saya</a>
    <a href="logout.php" class="danger"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
    </div>';
}

function renderFoot() {
    echo '<footer style="text-align:center;padding:28px 16px;color:var(--text3);font-size:13px;border-top:1px solid var(--border);margin-top:auto;">
    &copy; '.date('Y').' SmartFinder &mdash; Sistem Manajemen Barang Temuan Kampus
    </footer></body></html>';
}

function showAlert($type, $msg) {
    $icons=['success'=>'circle-check','error'=>'circle-xmark','info'=>'circle-info','warning'=>'triangle-exclamation'];
    $icon=$icons[$type]??'circle-info';
    return "<div class='alert alert-{$type}'><i class='fa-solid fa-{$icon}'></i><span>{$msg}</span></div>";
}

function buildPage($title, $sidebarActive, $content, $extra='') {
    renderHead($title, $extra);
    renderTopbar();
    echo '<div class="app-layout">';
    renderSidebar($sidebarActive);
    echo '<div class="main-content">'.$content.'</div>';
    echo '</div>';
    renderFoot();
}
