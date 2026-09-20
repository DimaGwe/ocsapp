<?php
if (!defined('BASE_PATH')) { http_response_code(404); exit; }
$currentLang = $_GET['lang'] ?? $_SESSION['language'] ?? $_SESSION['lang'] ?? 'fr';
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $_SESSION['language'] = $_GET['lang'];
}
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= $fr ? 'fr' : 'en' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? "OCSAPP - L'écosystème numérique du commerce d'ici" : 'OCSAPP - The Digital Ecosystem for Local Commerce' ?></title>
  <meta name="description" content="<?= $fr
      ? "OCSAPP relie marché, vendeurs, fournisseurs, acheteurs, entreprises et livreurs dans un seul écosystème numérique pour le commerce d'ici."
      : 'OCSAPP connects marketplace, sellers, suppliers, buyers, businesses and drivers within one digital ecosystem built for local commerce.'
  ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00B207">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    :root{
      --green:#00B207;
      --green-rgb:0,178,7;
      --green-dark:#0A2E14;
      --green-panel-1:#0B2F16;
      --green-panel-2:#0F4020;
      --white:#fff;
      --bg:#F7F8F7;
      --surface:#fff;
      --surface-soft:#F2F5F2;
      --border:#E5E7E4;
      --text:#17181A;
      --muted:#66706A;
      --muted-2:#919994;
      --dark:#075A0B;
      --dark-2:#0B4610;
      --panel-green:#075A0B;
      --panel-green-2:#0A4A10;
      --panel-green-soft:#0D6514;
      --shadow-sm:0 8px 28px rgba(16,24,18,.07);
      --shadow-md:0 18px 54px rgba(16,24,18,.11);
      --shadow-green:0 20px 60px rgba(var(--green-rgb),.14);
      --radius:22px;
      --radius-sm:14px;
      --max:1180px;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{
      font-family:'Inter',sans-serif;
      color:var(--text);
      background:var(--white);
      line-height:1.65;
      -webkit-font-smoothing:antialiased;
      overflow-x:hidden;
    }
    h1,h2,h3,h4,.brand-text,.btn,.eyebrow,.nav-link{
      font-family:'Poppins',sans-serif;
    }
    a{text-decoration:none;color:inherit}
    img{max-width:100%;display:block}
    .wrap{width:min(var(--max),calc(100% - 64px));margin-inline:auto}
    .section{padding:104px 0}
    .section-soft{background:var(--bg)}
    .eyebrow{
      display:inline-flex;align-items:center;gap:9px;
      color:var(--green);font-size:12px;font-weight:700;
      letter-spacing:.14em;text-transform:uppercase;margin-bottom:18px;
    }
    .eyebrow::before{
      content:"";width:28px;height:2px;border-radius:99px;background:var(--green)
    }
    .section-head{max-width:760px;margin-bottom:48px}
    .section-head.center{text-align:center;margin-inline:auto}
    .section-head.center .eyebrow{justify-content:center}
    .section-head.center .eyebrow::before{display:none}
    h1{font-size:clamp(42px,6vw,76px);line-height:1.03;letter-spacing:-.045em}
    h2{font-size:clamp(30px,4vw,48px);line-height:1.12;letter-spacing:-.035em}
    h3{line-height:1.25;letter-spacing:-.02em}
    .lead{font-size:18px;color:var(--muted);max-width:690px}
    .small{font-size:13px;color:var(--muted)}

    /* Beta */
    .beta{
      background:var(--dark);color:rgba(255,255,255,.72);
      min-height:34px;display:flex;align-items:center;justify-content:center;
      gap:10px;padding:6px 18px;font-size:12px;text-align:center;
      border-bottom:1px solid rgba(var(--green-rgb),.25)
    }
    .beta-badge{
      color:var(--green);border:1px solid rgba(var(--green-rgb),.45);
      background:rgba(var(--green-rgb),.10);
      border-radius:999px;padding:1px 8px;font-family:'Poppins',sans-serif;
      font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase
    }
    .beta a{color:#74E278;font-weight:600}

    /* Header */
    header{
      position:sticky;top:0;z-index:60;
      background:rgba(255,255,255,.94);
      border-bottom:1px solid var(--border);
      backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px)
    }
    .header-inner{
      min-height:76px;display:flex;align-items:center;gap:24px
    }
    .brand{display:flex;align-items:center;gap:11px;flex-shrink:0}
    .brand img{width:42px;height:42px;object-fit:contain;filter:drop-shadow(0 7px 15px rgba(var(--green-rgb),.14))}
    .brand-text{font-size:20px;font-weight:700;color:var(--green);letter-spacing:-.02em}
    nav{display:flex;align-items:center;gap:26px;margin-left:auto}
    .nav-link{font-size:13px;font-weight:600;color:#3E4541}
    .nav-link:hover{color:var(--green)}
    .lang{
      display:flex;align-items:center;border:1px solid var(--border);
      border-radius:999px;padding:3px;background:#FAFBFA
    }
    .lang a{font-size:11px;font-weight:700;padding:5px 8px;border-radius:999px;color:var(--muted)}
    .lang a.active{background:var(--text);color:white}
    .btn{
      display:inline-flex;align-items:center;justify-content:center;gap:9px;
      min-height:46px;padding:0 20px;border-radius:12px;font-size:13px;font-weight:600;
      border:1px solid transparent;transition:.2s ease
    }
    .btn-primary{background:var(--green);color:#fff;box-shadow:0 12px 28px rgba(var(--green-rgb),.22)}
    .btn-primary:hover{transform:translateY(-1px);box-shadow:0 16px 34px rgba(var(--green-rgb),.28)}
    .btn-secondary{border-color:var(--border);background:#fff;color:var(--text)}
    .btn-secondary:hover{border-color:#B8C0BA;box-shadow:var(--shadow-sm)}
    .mobile-toggle{display:none;margin-left:auto;border:0;background:none;font-size:21px;color:var(--text);cursor:pointer;padding:4px 6px}
    .mobile-menu{display:none;flex-direction:column;gap:2px;padding:10px 0 18px;border-top:1px solid var(--border)}
    .mobile-menu.open{display:flex}
    .mobile-menu-link{display:block;padding:12px 2px;font-size:14px;font-weight:600;color:#3E4541;border-bottom:1px solid var(--border)}
    .mobile-menu-link:hover{color:var(--green)}
    .mobile-menu-lang{display:flex;gap:8px;margin:14px 0 4px}
    .mobile-menu-lang a{font-size:12px;font-weight:700;padding:7px 16px;border-radius:999px;border:1px solid var(--border);color:var(--muted)}
    .mobile-menu-lang a.active{background:var(--text);color:#fff;border-color:var(--text)}
    .mobile-menu .btn{margin-top:14px}
    @media (min-width:761px){.mobile-menu{display:none!important}}

    /* Hero */
    .hero{
      position:relative;overflow:hidden;padding:92px 0 86px;
      background:
        radial-gradient(circle at 86% 18%,rgba(var(--green-rgb),.09),transparent 28%),
        linear-gradient(180deg,#fff 0%,#FAFCFA 100%)
    }
    .hero::before{
      content:"";position:absolute;inset:0;pointer-events:none;
      background-image:linear-gradient(rgba(15,35,20,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(15,35,20,.025) 1px,transparent 1px);
      background-size:44px 44px;mask-image:linear-gradient(to bottom,black,transparent 80%)
    }
    .hero-grid{display:grid;grid-template-columns:1.02fr .98fr;gap:62px;align-items:center;position:relative}
    .hero-copy .eyebrow{margin-bottom:22px}
    .hero h1 span{color:var(--green)}
    .hero-sub{font-size:19px;color:var(--muted);max-width:650px;margin:26px 0 30px}
    .hero-actions{display:flex;gap:12px;flex-wrap:wrap}
    .hero-note{display:flex;align-items:center;gap:9px;margin-top:24px;font-size:12.5px;color:var(--muted)}
    .hero-note i{color:var(--green)}

    /* Ecosystem visual */
    .ecosystem-card{
      position:relative;border-radius:30px;padding:28px;min-height:540px;
      background:
        radial-gradient(circle at 50% 45%,rgba(104,255,111,.16),transparent 34%),
        linear-gradient(155deg,var(--panel-green) 0%,var(--panel-green-2) 100%);
      border:1px solid rgba(255,255,255,.08);
      box-shadow:0 36px 90px rgba(6,17,10,.24);
      overflow:hidden
    }
    .ecosystem-card::before{
      content:"";position:absolute;inset:0;opacity:.45;
      background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);
      background-size:36px 36px
    }
    .eco-kicker{
      position:relative;z-index:2;color:#9AE49D;font-family:'Poppins',sans-serif;
      font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.14em
    }
    .eco-title{position:relative;z-index:2;color:#fff;font-size:22px;margin-top:7px;max-width:360px}
    .eco-stage{position:relative;height:400px;margin-top:18px}
    .eco-cluster{position:absolute;inset:0;transform:translateY(-24px)}
    .hub{
      position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
      width:150px;height:150px;border-radius:50%;display:grid;place-items:center;text-align:center;
      color:#fff;background:linear-gradient(145deg,#0D3818,#0A2411);
      border:1px solid rgba(var(--green-rgb),.75);
      box-shadow:0 0 0 12px rgba(var(--green-rgb),.035),0 0 55px rgba(var(--green-rgb),.18);
      z-index:4
    }
    .hub img{width:54px;height:54px;object-fit:contain;margin:0 auto 6px}
    .hub strong{display:block;font-family:'Poppins';font-size:13px}
    .hub span{display:block;font-size:10px;color:#9FB3A4;margin-top:2px}
    .node{
      position:absolute;width:146px;min-height:68px;padding:11px 12px;border-radius:15px;
      background:rgba(255,255,255,.10);border:1px solid rgba(255,255,255,.15);
      color:#fff;display:flex;align-items:center;gap:10px;z-index:3;
      backdrop-filter:blur(8px);cursor:pointer;transition:background .2s ease,border-color .2s ease
    }
    .node:hover{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.28)}
    .node .ico{
      width:46px;height:46px;border-radius:12px;background:#070B08;
      border:1px solid rgba(255,255,255,.08);padding:2px;overflow:hidden;
      box-shadow:0 8px 18px rgba(0,0,0,.18);
      display:grid;place-items:center;flex:0 0 auto
    }
    .node .ico img{width:100%;height:100%;object-fit:cover;border-radius:9px}
    .node strong{font-family:'Poppins';font-size:11px;display:block;line-height:1.25}
    .node span{font-size:9.5px;color:#9BB0A0;display:block;margin-top:2px}
    .n1{left:4%;top:8%}.n2{right:4%;top:8%}.n3{left:2%;top:42%}.n4{right:2%;top:42%}.n5{left:8%;bottom:6%}.n6{right:8%;bottom:6%}
    .eco-lines{position:absolute;inset:0;z-index:1;width:100%;height:100%}
    .eco-lines line{stroke:rgba(141,245,146,.44);stroke-width:1.1;stroke-dasharray:4 6}
    .eco-lines circle{fill:#8CF491}
    .eco-caption{
      position:absolute;left:50%;bottom:5px;transform:translateX(-50%);z-index:3;
      color:#fff;font-size:10px;line-height:1.3;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap
    }

    /* Intro */
    .intro-strip{
      border-top:1px solid var(--border);border-bottom:1px solid var(--border);
      background:#fff
    }
    .intro-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:70px;align-items:center;padding:58px 0}
    .intro-grid h2{font-size:31px}
    .intro-grid p{color:var(--muted);font-size:15px}
    .mini-points{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
    .mini-point{padding:16px;border:1px solid var(--border);border-radius:14px;background:#FBFCFB}
    .mini-point strong{display:block;color:var(--green);font-family:'Poppins';font-size:18px}
    .mini-point span{display:block;font-size:11px;color:var(--muted);margin-top:2px}

    /* Central cards */
    .central-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
    .central-card{
      position:relative;padding:28px;border:1px solid var(--border);border-radius:var(--radius);
      background:#fff;box-shadow:0 6px 24px rgba(17,24,18,.04);transition:.22s ease;
      overflow:hidden;min-height:268px;display:flex;flex-direction:column
    }
    .central-card::after{
      content:"";position:absolute;width:120px;height:120px;border-radius:50%;right:-56px;top:-56px;
      background:rgba(var(--green-rgb),.055)
    }
    .central-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md);border-color:#D5DDD6}
    .central-icon{
      width:88px;height:88px;border-radius:20px;display:grid;place-items:center;
      background:#080C09;border:1px solid rgba(255,255,255,.06);
      box-shadow:0 12px 30px rgba(13,28,16,.16), inset 0 0 0 1px rgba(var(--green-rgb),.08);
      overflow:hidden;padding:4px
    }
    .central-icon img{
      width:100%;height:100%;object-fit:cover;border-radius:16px;
      filter:saturate(1.04) contrast(1.02);
    }
    .central-card h3{font-size:20px;margin:18px 0 8px}
    .central-card p{font-size:14px;color:var(--muted);margin-bottom:20px;max-width:320px}
    .central-card .link{margin-top:auto;display:inline-flex;align-items:center;gap:8px;color:var(--green);font-family:'Poppins';font-size:12.5px;font-weight:600}
    .central-card .link i{font-size:10px;transition:.2s}
    .central-card:hover .link i{transform:translateX(3px)}
    .role-tag{font-size:10px;color:var(--muted-2);text-transform:uppercase;letter-spacing:.1em;font-weight:600;margin-top:8px}

    /* Flow */
    .flow-section{
      background:linear-gradient(145deg,#075A0B 0%,#0A4510 56%,#08340C 100%);
      color:#fff;position:relative;overflow:hidden
    }
    .flow-section::before{
      content:"";position:absolute;inset:0;background:radial-gradient(circle at 75% 18%,rgba(var(--green-rgb),.14),transparent 27%)
    }
    .flow-section .eyebrow{color:#72DD77}
    .flow-section .section-head p{color:#AAB8AE}
    .flow-track{position:relative;display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-top:52px}
    .flow-track::before{
      content:"";position:absolute;left:7%;right:7%;top:42px;height:1px;
      background:linear-gradient(90deg,transparent,rgba(var(--green-rgb),.55),rgba(var(--green-rgb),.55),transparent)
    }
    .flow-step{position:relative;z-index:2;text-align:center}
    .flow-num{
      width:84px;height:84px;border-radius:50%;margin:0 auto 18px;display:grid;place-items:center;
      background:linear-gradient(145deg,#38D844,#15A821 58%,#08750F 100%);
      border:1px solid rgba(255,255,255,.20);
      box-shadow:0 8px 0 #064E0B,0 15px 30px rgba(0,0,0,.20),inset 1px 1px 0 rgba(255,255,255,.28);
      color:#fff;font-size:22px;transform:translateY(-3px)
    }
    .flow-step h3{font-size:14px}
    .flow-step p{font-size:11px;color:#8FA094;margin:7px auto 0;max-width:170px}
    .delivery-layer{
      margin-top:54px;border:1px solid rgba(var(--green-rgb),.22);
      background:rgba(var(--green-rgb),.055);border-radius:18px;padding:22px 24px;
      display:flex;align-items:center;gap:18px
    }
    .delivery-layer .delivery-icon{
      width:50px;height:50px;border-radius:14px;
      background:linear-gradient(145deg,#38D844,#15A821 58%,#08750F 100%);
      border:1px solid rgba(255,255,255,.20);
      box-shadow:0 8px 0 #064E0B,0 15px 30px rgba(0,0,0,.20),inset 1px 1px 0 rgba(255,255,255,.28);
      display:grid;place-items:center;color:#fff;font-size:20px;flex:0 0 auto
    }
    .delivery-layer h3{font-size:15px;margin-bottom:2px}
    .delivery-layer p{font-size:12px;color:#98AA9E}
    .delivery-layer .objective{
      margin-left:auto;white-space:nowrap;border:1px solid rgba(var(--green-rgb),.28);
      border-radius:999px;padding:7px 11px;font-size:10px;color:#81DF85;text-transform:uppercase;letter-spacing:.08em
    }

    /* Value */
    .value-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:28px}
    .value-panel{
      padding:42px;border-radius:26px;border:1px solid var(--border);background:#fff;
      box-shadow:var(--shadow-sm)
    }
    .value-panel.dark{
      background:linear-gradient(145deg,#0A5A10,#083D0C);
      border-color:#17691C;color:#fff
    }
    .value-list{display:grid;gap:22px;margin-top:28px}
    .value-item{display:grid;grid-template-columns:42px 1fr;gap:14px;align-items:start}
    .value-item .vicon{
      position:relative;width:42px;height:42px;border-radius:12px;display:grid;place-items:center;
      background:linear-gradient(145deg,#F8FFF8 0%,#CFEFD1 48%,#A7DCAA 100%);
      border:1px solid rgba(0,178,7,.22);color:#08760E;
      box-shadow:0 8px 0 #87C98B,0 13px 22px rgba(7,69,16,.15),inset 1px 1px 0 rgba(255,255,255,.9);
      transform:translateY(-3px);
    }
    .dark .value-item .vicon{
      background:linear-gradient(145deg,#42DF4C,#139B1D 62%,#08720E);color:#fff;
      border-color:rgba(255,255,255,.16);
      box-shadow:0 7px 0 #064C0B,0 13px 24px rgba(0,0,0,.18),inset 1px 1px 0 rgba(255,255,255,.24);
    }
    .value-item h3{font-size:14px;margin-bottom:3px}
    .value-item p{font-size:12.5px;color:var(--muted)}
    .dark .value-item p{color:#91A295}
    .local-block{
      margin-top:28px;padding:22px;border-radius:18px;background:#F5F8F5;border:1px solid #E7ECE7
    }
    .local-block strong{font-family:'Poppins';font-size:14px;color:var(--green)}
    .local-block p{font-size:12.5px;color:var(--muted);margin-top:5px}

    /* CTA */
    .cta-wrap{
      border-radius:30px;padding:64px;background:
        radial-gradient(circle at 90% 0%,rgba(255,255,255,.12),transparent 28%),
        linear-gradient(135deg,#0A6312,#08390C);
      color:#fff;display:grid;grid-template-columns:1fr auto;gap:40px;align-items:center
    }
    .cta-wrap h2{max-width:700px}
    .cta-wrap p{color:#9DB0A1;margin-top:14px;max-width:650px}
    .cta-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end}
    .btn-light{background:#fff;color:#102016}
    .btn-dark-outline{border-color:rgba(255,255,255,.18);color:#fff;background:rgba(255,255,255,.04)}

    /* Footer */
    footer{background:#fff;border-top:1px solid var(--border);padding:68px 0 30px}
    .footer-top{display:grid;grid-template-columns:1.65fr .85fr 1fr .85fr;gap:46px;padding-bottom:42px;border-bottom:1px solid var(--border)}
    .footer-brand{display:flex;align-items:center;gap:10px;margin-bottom:14px}
    .footer-brand img{width:38px;height:38px;object-fit:contain}
    .footer-logo-text{font-family:'Poppins';font-weight:700;color:var(--green);font-size:18px}
    .foot-tagline{font-size:13.5px;color:var(--muted);max-width:340px;margin-bottom:12px}
    .footer-brand-col p:not(.foot-tagline){font-size:11.5px;color:var(--muted-2);margin-bottom:4px;line-height:1.6}
    .footer-col h5{font-family:'Poppins';font-size:11.5px;color:var(--text);margin-bottom:14px;letter-spacing:.06em;text-transform:uppercase}
    .footer-col a{display:block;color:var(--muted);font-size:13px;margin-bottom:9px}
    .footer-col a:hover{color:var(--green)}
    .footer-bottom{display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;align-items:center;padding-top:24px}
    .footer-bottom p{font-size:12px;color:var(--muted-2)}
    .footer-legal{display:flex;flex-wrap:wrap;gap:4px 16px}
    .footer-legal a{color:var(--muted-2);font-size:11.5px}
    .footer-legal a:hover{color:var(--green)}

    /* Responsive */
    @media (max-width:1060px){
      .hero-grid,.value-grid{grid-template-columns:1fr}
      .ecosystem-card{max-width:720px;width:100%;margin:auto}
      .central-grid{grid-template-columns:repeat(2,1fr)}
      nav .nav-link{display:none}
      .flow-track{grid-template-columns:repeat(3,1fr);row-gap:34px}
      .flow-track::before{display:none}
      .footer-top{grid-template-columns:1.25fr 1fr 1fr}
      .footer-brand-col{grid-column:1/-1}
    }
    @media (max-width:760px){
      .wrap{width:min(100% - 40px,var(--max))}
      .section{padding:76px 0}
      .hero{padding:68px 0 64px}
      nav{gap:10px}
      nav .lang{display:none}
      .header-join{display:none}
      .mobile-toggle{display:block}
      .intro-grid{grid-template-columns:1fr;gap:28px}
      .mini-points{grid-template-columns:1fr}
      .central-grid{grid-template-columns:1fr}
      .flow-track{grid-template-columns:1fr 1fr}
      .delivery-layer{align-items:flex-start;flex-wrap:wrap}
      .delivery-layer .objective{margin-left:68px}
      .cta-wrap{grid-template-columns:1fr;padding:42px 28px}
      .cta-actions{justify-content:flex-start}
      .footer-top{grid-template-columns:1fr 1fr}
      .footer-brand-col{grid-column:1/-1}
    }
    @media (max-width:560px){
      .beta .full{display:none}
      .brand-text{font-size:18px}
      .brand img{width:38px;height:38px}
      .header-inner{min-height:68px}
      .hero-grid{gap:40px}
      .hero-sub{font-size:16px}
      .ecosystem-card{padding:22px;min-height:560px}
      .eco-stage{height:435px}
      .hub{width:126px;height:126px}
      .hub img{width:46px;height:46px}
      .node{width:122px;min-height:62px;padding:9px}
      .node .ico{width:38px;height:38px}
      .node strong{font-size:9.5px}
      .node span{display:none}
      .n1{left:1%;top:7%}.n2{right:1%;top:7%}.n3{left:0;top:42%}.n4{right:0;top:42%}.n5{left:4%;bottom:7%}.n6{right:4%;bottom:7%}
      .eco-caption{font-size:8.5px}
      .flow-track{grid-template-columns:1fr}
      .flow-num{width:72px;height:72px}
      .central-icon{width:76px;height:76px}
      .footer-top{grid-template-columns:1fr}
      .footer-brand-col{grid-column:auto}
      .footer-bottom{align-items:flex-start;flex-direction:column}
      .footer-legal{gap:6px 14px}
    }

    @media (prefers-reduced-motion:no-preference){
      .node{animation:floatNode 5s ease-in-out infinite}
      .n2,.n5{animation-delay:.8s}.n3,.n6{animation-delay:1.6s}
      @keyframes floatNode{0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)}}
    }
    @media (prefers-reduced-motion:reduce){
      *{scroll-behavior:auto!important;animation:none!important;transition:none!important}
    }
    a:focus-visible,button:focus-visible{outline:2px solid var(--green);outline-offset:3px}
  </style>
</head>
<body>

  <div class="beta">
    <span class="beta-badge">B&ecirc;ta</span>
    <span class="full"><?= $fr
        ? 'Plateforme en cours de développement. Certaines fonctionnalités ne sont pas encore disponibles.'
        : 'Platform under development. Some features are not yet available.'
    ?></span>
    <a href="<?= url('waitlist') ?>"><?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?></a>
  </div>

  <header>
    <div class="wrap header-inner">
      <a class="brand" href="<?= url('') ?>" aria-label="<?= $fr ? 'OCSAPP - Accueil' : 'OCSAPP - Home' ?>">
        <img src="<?= asset('images/logo.png') ?>" alt="Logo OCSAPP">
        <span class="brand-text">OCSAPP</span>
      </a>

      <nav aria-label="<?= $fr ? 'Navigation principale' : 'Main navigation' ?>">
        <a class="nav-link" href="#ecosysteme"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
        <a class="nav-link" href="#centrales"><?= $fr ? 'Nos Centrales' : 'Our Centrals' ?></a>
        <a class="nav-link" href="#fonctionnement"><?= $fr ? 'Comment ça fonctionne' : 'How it works' ?></a>
        <a class="nav-link" href="<?= url('about') ?>"><?= $fr ? 'À propos' : 'About' ?></a>
        <div class="lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
          <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
          <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
        </div>
        <a class="btn btn-secondary" href="<?= url('login') ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> <?= $fr ? 'Se connecter' : 'Sign in' ?></a>
        <a class="btn btn-primary header-join" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
        <button type="button" class="mobile-toggle" id="navToggle" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenu">
          <i class="fa-solid fa-bars"></i>
        </button>
      </nav>
    </div>
    <div class="wrap">
      <div class="mobile-menu" id="mobileMenu">
        <a class="mobile-menu-link" href="#ecosysteme"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
        <a class="mobile-menu-link" href="#centrales"><?= $fr ? 'Nos Centrales' : 'Our Centrals' ?></a>
        <a class="mobile-menu-link" href="#fonctionnement"><?= $fr ? 'Comment ça fonctionne' : 'How it works' ?></a>
        <a class="mobile-menu-link" href="<?= url('about') ?>"><?= $fr ? 'À propos' : 'About' ?></a>
        <div class="mobile-menu-lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
          <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
          <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
        </div>
        <a class="btn btn-primary" style="width:100%" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
      </div>
    </div>
  </header>

  <main>
    <section class="hero" id="ecosysteme">
      <div class="wrap hero-grid">
        <div class="hero-copy">
          <span class="eyebrow"><?= $fr ? "Un seul système. Plusieurs façons d'y entrer." : 'One system. Multiple ways to enter.' ?></span>
          <?php if ($fr): ?>
            <h1>Le commerce d'ici.<br><span>Un seul écosystème.</span></h1>
          <?php else: ?>
            <h1>Local commerce.<br><span>One ecosystem.</span></h1>
          <?php endif; ?>
          <p class="hero-sub">
            <?= $fr
              ? "OCSAPP relie marché, vendeurs, fournisseurs, acheteurs, entreprises et livreurs dans une seule infrastructure numérique conçue pour faire circuler le commerce local."
              : 'OCSAPP connects marketplace, sellers, suppliers, buyers, businesses and drivers within a single digital infrastructure built to keep local commerce moving.'
            ?>
          </p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#centrales"><?= $fr ? "Explorer l'écosystème" : 'Explore the ecosystem' ?> <i class="fa-solid fa-arrow-down"></i></a>
            <a class="btn btn-secondary" href="<?= url('waitlist') ?>"><?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?></a>
          </div>
          <div class="hero-note">
            <i class="fa-solid fa-circle-nodes" aria-hidden="true"></i>
            <span><?= $fr
              ? "Choisissez votre point d'entrée. Le reste de l'écosystème demeure connecté."
              : 'Choose your point of entry. The rest of the ecosystem stays connected.'
            ?></span>
          </div>
        </div>

        <div class="ecosystem-card" aria-label="<?= $fr ? "Schéma de l'écosystème OCSAPP" : 'OCSAPP ecosystem diagram' ?>">
          <div class="eco-kicker"><?= $fr ? 'Architecture OCSAPP' : 'OCSAPP Architecture' ?></div>
          <h2 class="eco-title"><?= $fr ? 'Six Centrales. Une infrastructure commune.' : 'Six Centrals. One shared infrastructure.' ?></h2>

          <div class="eco-stage">
            <div class="eco-cluster">
              <svg class="eco-lines" viewBox="0 0 500 400" preserveAspectRatio="none" aria-hidden="true">
                <line x1="250" y1="200" x2="82" y2="55"></line><circle cx="82" cy="55" r="3"></circle>
                <line x1="250" y1="200" x2="418" y2="55"></line><circle cx="418" cy="55" r="3"></circle>
                <line x1="250" y1="200" x2="72" y2="198"></line><circle cx="72" cy="198" r="3"></circle>
                <line x1="250" y1="200" x2="428" y2="198"></line><circle cx="428" cy="198" r="3"></circle>
                <line x1="250" y1="200" x2="105" y2="345"></line><circle cx="105" cy="345" r="3"></circle>
                <line x1="250" y1="200" x2="395" y2="345"></line><circle cx="395" cy="345" r="3"></circle>
              </svg>

              <div class="hub">
                <div>
                  <img src="<?= asset('images/logo.png') ?>" alt="">
                  <strong>OCSAPP</strong>
                  <span><?= $fr ? 'Infrastructure commune' : 'Shared infrastructure' ?></span>
                </div>
              </div>

              <a class="node n1" href="<?= url('home') ?>">
                <div class="ico"><img src="<?= asset('images/centrals/icon-marketplace.jpg') ?>" alt=""></div>
                <div><strong><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></strong><span><?= $fr ? 'Découvrir & magasiner' : 'Discover & shop' ?></span></div>
              </a>
              <a class="node n2" href="<?= url('seller-central') ?>">
                <div class="ico"><img src="<?= asset('images/centrals/icon-seller.jpg') ?>" alt=""></div>
                <div><strong><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></strong><span><?= $fr ? 'Vendre & gérer' : 'Sell & manage' ?></span></div>
              </a>
              <a class="node n3" href="<?= url('supplier-central') ?>">
                <div class="ico"><img src="<?= asset('images/centrals/icon-supplier.jpg') ?>" alt=""></div>
                <div><strong><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></strong><span><?= $fr ? 'Approvisionner' : 'Supply' ?></span></div>
              </a>
              <a class="node n4" href="<?= url('buyer-central') ?>">
                <div class="ico"><img src="<?= asset('images/centrals/icon-buyer.jpg') ?>" alt=""></div>
                <div><strong><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></strong><span><?= $fr ? 'Acheter & suivre' : 'Buy & track' ?></span></div>
              </a>
              <a class="node n5" href="<?= url('distribution') ?>">
                <div class="ico"><img src="<?= asset('images/centrals/icon-business.jpg') ?>" alt=""></div>
                <div><strong><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></strong><span><?= $fr ? "S'approvisionner & distribuer" : 'Source & distribute' ?></span></div>
              </a>
              <a class="node n6" href="<?= url('driver-central') ?>">
                <div class="ico"><img src="<?= asset('images/centrals/icon-driver.jpg') ?>" alt=""></div>
                <div><strong><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></strong><span><?= $fr ? 'Livrer & gagner' : 'Deliver & earn' ?></span></div>
              </a>
            </div>
            <div class="eco-caption"><?= $fr ? "UN ÉCOSYSTÈME · PLUSIEURS POINTS D'ENTRÉE" : 'ONE ECOSYSTEM · MULTIPLE ENTRY POINTS' ?></div>
          </div>
        </div>
      </div>
    </section>

    <section class="intro-strip">
      <div class="wrap intro-grid">
        <div>
          <span class="eyebrow"><?= $fr ? 'La différence OCSAPP' : 'The OCSAPP difference' ?></span>
          <?php if ($fr): ?>
            <h2>Pas six applications isolées.<br>Un commerce connecté de bout en bout.</h2>
          <?php else: ?>
            <h2>Not six isolated apps.<br>Commerce connected end to end.</h2>
          <?php endif; ?>
        </div>
        <div>
          <p>
            <?= $fr
              ? "Chaque Centrale répond à un rôle précis, mais toutes font partie de la même architecture. Le vendeur, le fournisseur, l'entreprise, l'acheteur et le livreur n'entrent pas dans des silos : ils participent au même écosystème commercial."
              : 'Each Central serves a specific role, but all are part of the same architecture. Sellers, suppliers, businesses, buyers and drivers are not siloed apart - they all take part in the same commercial ecosystem.'
            ?>
          </p>
          <div class="mini-points" style="margin-top:22px">
            <div class="mini-point"><strong>6</strong><span><?= $fr ? 'Centrales connectées' : 'Connected Centrals' ?></span></div>
            <div class="mini-point"><strong>1</strong><span><?= $fr ? 'Infrastructure OCSAPP' : 'OCSAPP infrastructure' ?></span></div>
            <div class="mini-point"><strong>1</strong><span><?= $fr ? 'Écosystème local' : 'Local ecosystem' ?></span></div>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-soft" id="centrales">
      <div class="wrap">
        <div class="section-head center">
          <span class="eyebrow"><?= $fr ? "Votre point d'entrée" : 'Your point of entry' ?></span>
          <h2><?= $fr ? 'Entrez dans OCSAPP selon votre rôle.' : 'Enter OCSAPP through the role that fits you.' ?></h2>
          <p class="lead" style="margin:16px auto 0">
            <?= $fr
              ? "Commencez par la Centrale qui vous correspond aujourd'hui. Les autres fonctions de l'écosystème restent reliées autour de votre activité."
              : "Start with the Central that fits you today. The rest of the ecosystem's functions stay connected around your activity."
            ?>
          </p>
        </div>

        <div class="central-grid">
          <a class="central-card" href="<?= url('home') ?>">
            <div class="central-icon"><img src="<?= asset('images/centrals/icon-marketplace.jpg') ?>" alt="<?= $fr ? 'Icône officielle Marché Central' : 'Official Marketplace Central icon' ?>"></div>
            <div class="role-tag"><?= $fr ? 'Découvrir le commerce d\'ici' : 'Discover local commerce' ?></div>
            <h3><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></h3>
            <p><?= $fr
              ? 'Parcourez les boutiques, commerces et offres locales réunis dans le marché OCSAPP.'
              : 'Browse local shops, merchants and offers brought together in the OCSAPP marketplace.'
            ?></p>
            <span class="link"><?= $fr ? 'Explorer le Marché' : 'Explore Marketplace Central' ?> <i class="fa-solid fa-arrow-right"></i></span>
          </a>

          <a class="central-card" href="<?= url('seller-central') ?>">
            <div class="central-icon"><img src="<?= asset('images/centrals/icon-seller.jpg') ?>" alt="<?= $fr ? 'Icône officielle Vendeur Central' : 'Official Seller Central icon' ?>"></div>
            <div class="role-tag"><?= $fr ? 'Pour les commerces' : 'For merchants' ?></div>
            <h3><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></h3>
            <p><?= $fr
              ? 'Ouvrez votre présence sur OCSAPP, gérez vos commandes et développez votre activité locale.'
              : 'Build your presence on OCSAPP, manage orders and grow your local business.'
            ?></p>
            <span class="link"><?= $fr ? 'Découvrir Vendeur Central' : 'Discover Seller Central' ?> <i class="fa-solid fa-arrow-right"></i></span>
          </a>

          <a class="central-card" href="<?= url('supplier-central') ?>">
            <div class="central-icon"><img src="<?= asset('images/centrals/icon-supplier.jpg') ?>" alt="<?= $fr ? 'Icône officielle Fournisseur Central' : 'Official Supplier Central icon' ?>"></div>
            <div class="role-tag"><?= $fr ? 'Pour les fournisseurs' : 'For suppliers' ?></div>
            <h3><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></h3>
            <p><?= $fr
              ? "Présentez votre offre, approvisionnez l'écosystème et développez votre réseau commercial."
              : 'Present your offering, supply the ecosystem and grow your commercial network.'
            ?></p>
            <span class="link"><?= $fr ? 'Découvrir Fournisseur Central' : 'Discover Supplier Central' ?> <i class="fa-solid fa-arrow-right"></i></span>
          </a>

          <a class="central-card" href="<?= url('buyer-central') ?>">
            <div class="central-icon"><img src="<?= asset('images/centrals/icon-buyer.jpg') ?>" alt="<?= $fr ? 'Icône officielle Acheteur Central' : 'Official Buyer Central icon' ?>"></div>
            <div class="role-tag"><?= $fr ? 'Pour les acheteurs' : 'For buyers' ?></div>
            <h3><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></h3>
            <p><?= $fr
              ? 'Magasinez localement, suivez vos commandes et accédez à votre expérience d\'achat OCSAPP.'
              : 'Shop locally, track your orders and access your OCSAPP buying experience.'
            ?></p>
            <span class="link"><?= $fr ? 'Découvrir Acheteur Central' : 'Discover Buyer Central' ?> <i class="fa-solid fa-arrow-right"></i></span>
          </a>

          <a class="central-card" href="<?= url('distribution') ?>">
            <div class="central-icon"><img src="<?= asset('images/centrals/icon-business.jpg') ?>" alt="<?= $fr ? 'Icône officielle Entreprise Centrale' : 'Official Business Central icon' ?>"></div>
            <div class="role-tag"><?= $fr ? 'Pour les organisations' : 'For organizations' ?></div>
            <h3><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></h3>
            <p><?= $fr
              ? "Centralisez vos besoins d'approvisionnement, de distribution et de livraison d'entreprise."
              : 'Centralize your business sourcing, distribution and delivery needs.'
            ?></p>
            <span class="link"><?= $fr ? 'Découvrir Entreprise Centrale' : 'Discover Business Central' ?> <i class="fa-solid fa-arrow-right"></i></span>
          </a>

          <a class="central-card" href="<?= url('driver-central') ?>">
            <div class="central-icon"><img src="<?= asset('images/centrals/icon-driver.jpg') ?>" alt="<?= $fr ? 'Icône officielle Livreur Central' : 'Official Driver Central icon' ?>"></div>
            <div class="role-tag"><?= $fr ? 'Pour les livreurs' : 'For drivers' ?></div>
            <h3><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></h3>
            <p><?= $fr
              ? 'Accédez au volet livraison OCSAPP, choisissez vos disponibilités et gérez votre activité de livraison.'
              : 'Access OCSAPP delivery operations, choose your availability and manage your delivery activity.'
            ?></p>
            <span class="link"><?= $fr ? 'Découvrir Livreur Central' : 'Explore Driver Central' ?> <i class="fa-solid fa-arrow-right"></i></span>
          </a>
        </div>
      </div>
    </section>

    <section class="section flow-section" id="fonctionnement">
      <div class="wrap">
        <div class="section-head">
          <span class="eyebrow"><?= $fr ? 'Le commerce en mouvement' : 'Commerce in motion' ?></span>
          <h2><?= $fr ? "Une activité peut traverser tout l'écosystème." : 'One activity can move through the entire ecosystem.' ?></h2>
          <p class="lead" style="margin-top:16px">
            <?= $fr
              ? "OCSAPP est conçu pour relier les rôles qui participent au commerce local, plutôt que de les séparer dans des outils indépendants."
              : 'OCSAPP is built to connect the roles that take part in local commerce, rather than separating them into independent tools.'
            ?>
          </p>
        </div>

        <div class="flow-track">
          <div class="flow-step">
            <div class="flow-num"><i class="fa-solid fa-boxes-stacked"></i></div>
            <h3><?= $fr ? 'Approvisionnement' : 'Supply' ?></h3>
            <p><?= $fr ? 'Les fournisseurs et entreprises alimentent le réseau commercial.' : 'Suppliers and businesses feed the commerce network.' ?></p>
          </div>
          <div class="flow-step">
            <div class="flow-num"><i class="fa-solid fa-tag"></i></div>
            <h3><?= $fr ? 'Mise en marché' : 'Marketplace presence' ?></h3>
            <p><?= $fr ? 'Les vendeurs présentent leurs produits et leur commerce.' : 'Sellers present their products and businesses.' ?></p>
          </div>
          <div class="flow-step">
            <div class="flow-num"><i class="fa-solid fa-store"></i></div>
            <h3><?= $fr ? 'Découverte' : 'Discovery' ?></h3>
            <p><?= $fr ? 'Le Marché rassemble les offres accessibles aux acheteurs.' : 'Marketplace Central brings together offers available to buyers.' ?></p>
          </div>
          <div class="flow-step">
            <div class="flow-num"><i class="fa-solid fa-bag-shopping"></i></div>
            <h3><?= $fr ? 'Commande' : 'Order' ?></h3>
            <p><?= $fr ? "L'acheteur choisit, commande et suit son achat." : 'The buyer chooses, orders and tracks the purchase.' ?></p>
          </div>
          <div class="flow-step">
            <div class="flow-num"><i class="fa-solid fa-route"></i></div>
            <h3><?= $fr ? 'Livraison' : 'Delivery' ?></h3>
            <p><?= $fr ? 'La couche livraison relie la commande à sa destination.' : 'The delivery layer connects the order to its destination.' ?></p>
          </div>
        </div>

        <div class="delivery-layer">
          <div class="delivery-icon"><i class="fa-solid fa-leaf"></i></div>
          <div>
            <h3><?= $fr
              ? "La livraison fait partie de l'infrastructure - elle n'est pas ajoutée à la fin."
              : 'Delivery is part of the infrastructure - it is not added at the end.'
            ?></h3>
            <p><?= $fr
              ? "OCSAPP intègre la Livreur Central · ODA au fonctionnement de l'écosystème, avec un objectif de progression vers la livraison zéro émission."
              : 'OCSAPP integrates Driver Central · ODA into the ecosystem, with the objective of progressing toward zero-emission delivery.'
            ?></p>
          </div>
          <div class="objective"><?= $fr ? 'Objectif zéro émission' : 'Zero-emission objective' ?></div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="section-head center">
          <span class="eyebrow"><?= $fr ? 'Pourquoi une infrastructure commune' : 'Why shared infrastructure matters' ?></span>
          <h2><?= $fr ? 'Une expérience cohérente, quel que soit votre rôle.' : 'One coherent experience, whatever your role.' ?></h2>
        </div>

        <div class="value-grid">
          <div class="value-panel">
            <h3 style="font-size:23px"><?= $fr ? 'Un système pensé autour des relations commerciales.' : 'A system built around commercial relationships.' ?></h3>
            <div class="value-list">
              <div class="value-item">
                <div class="vicon"><i class="fa-solid fa-circle-nodes"></i></div>
                <div><h3><?= $fr ? 'Des rôles reliés' : 'Connected roles' ?></h3><p><?= $fr ? 'Chaque Centrale a son propre parcours, tout en restant reliée aux autres parties de l\'écosystème.' : 'Each Central has its own journey while staying connected to the rest of the ecosystem.' ?></p></div>
              </div>
              <div class="value-item">
                <div class="vicon"><i class="fa-solid fa-layer-group"></i></div>
                <div><h3><?= $fr ? 'Une architecture commune' : 'Shared architecture' ?></h3><p><?= $fr ? 'Une identité, une logique de navigation et une infrastructure cohérentes à travers OCSAPP.' : 'One identity, navigation logic and coherent infrastructure across OCSAPP.' ?></p></div>
              </div>
              <div class="value-item">
                <div class="vicon"><i class="fa-solid fa-location-dot"></i></div>
                <div><h3><?= $fr ? "Le commerce d'ici au centre" : 'Local commerce at the center' ?></h3><p><?= $fr ? "L'écosystème est construit autour des commerces, acheteurs, fournisseurs, entreprises et livreurs qui participent à l'économie locale." : 'The ecosystem is built around merchants, buyers, suppliers, businesses and drivers participating in the local economy.' ?></p></div>
              </div>
            </div>
          </div>

          <div class="value-panel dark">
            <span class="eyebrow"><?= $fr ? 'Une seule source' : 'One source' ?></span>
            <h3 style="font-size:25px"><?= $fr ? "Vous n'avez pas à comprendre six plateformes pour comprendre OCSAPP." : 'You do not need to understand six platforms to understand OCSAPP.' ?></h3>
            <p style="color:#95A89A;font-size:14px;margin-top:14px">
              <?= $fr ? 'Votre rôle détermine votre point d\'entrée. OCSAPP fournit le système autour.' : 'Your role determines your point of entry. OCSAPP provides the system around it.' ?>
            </p>
            <div class="local-block">
              <strong><?= $fr ? 'Commencez là où vous êtes.' : 'Start where you are.' ?></strong>
              <p><?= $fr
                ? 'Vendeur, acheteur, fournisseur, entreprise ou livreur : entrez par votre Centrale et évoluez dans le même écosystème.'
                : 'Seller, buyer, supplier, business or driver: enter through your Central and operate within the same ecosystem.'
              ?></p>
            </div>
            <a class="btn btn-primary" style="margin-top:24px" href="#centrales"><?= $fr ? 'Choisir ma Centrale' : 'Choose my Central' ?></a>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-soft">
      <div class="wrap">
        <div class="cta-wrap">
          <div>
            <span class="eyebrow">OCSAPP</span>
            <h2><?= $fr ? 'Votre commerce. Votre rôle. Un seul écosystème.' : 'Your commerce. Your role. One ecosystem.' ?></h2>
            <p><?= $fr
              ? "Explorez la Centrale qui vous correspond ou rejoignez la liste d'attente pour suivre l'évolution de la plateforme."
              : "Explore the Central that fits you or join the waitlist to follow the platform's evolution."
            ?></p>
          </div>
          <div class="cta-actions">
            <a class="btn btn-light" href="#centrales"><?= $fr ? 'Explorer les Centrales' : 'Explore the Centrals' ?></a>
            <a class="btn btn-dark-outline" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="wrap">
      <div class="footer-top">
        <div class="footer-brand-col">
          <div class="footer-brand">
            <img alt="Logo OCSAPP" src="<?= asset('images/logo.png') ?>">
            <span class="footer-logo-text">OCSAPP</span>
          </div>
          <p class="foot-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : 'The all-in-one digital infrastructure for local commerce.' ?></p>
          <p><?= $fr
            ? 'OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n<sup>o</sup> de société 1750354-7) · Numéro d\'entreprise du Québec (NEQ) 1181584997'
            : 'OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997'
          ?></p>
          <p><?= $fr ? 'Siège social : Laval, Québec (H7H)' : 'Registered office: Laval, Québec (H7H)' ?></p>
        </div>

        <div class="footer-col">
          <h5><?= $fr ? 'Apprenez à nous connaître' : 'Get to Know Us' ?></h5>
          <a href="<?= url('about') ?>"><?= $fr ? "À propos d'OCSAPP" : 'About OCSAPP' ?></a>
          <a href="<?= url('contact') ?>"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
        </div>

        <div class="footer-col">
          <h5><?= $fr ? 'Écosystème OCSAPP' : 'OCSAPP Ecosystem' ?></h5>
          <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
          <a href="<?= url('buyer-central') ?>"><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></a>
          <a href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
          <a href="<?= url('supplier-central') ?>"><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></a>
          <a href="<?= url('driver-central') ?>"><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></a>
          <a href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
        </div>

        <div class="footer-col">
          <h5><?= $fr ? 'Connectez-vous avec nous' : 'Connect With Us' ?></h5>
          <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a>
          <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a>
          <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a>
        </div>
      </div>

      <div class="footer-bottom">
        <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
        <div class="footer-legal">
          <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
          <a href="<?= url('terms') ?>"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
          <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique de cookies' : 'Cookie Policy' ?></a>
          <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a>
          <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    (function(){
      var toggle = document.getElementById('navToggle');
      var menu = document.getElementById('mobileMenu');
      if (!toggle || !menu) return;
      toggle.addEventListener('click', function(){
        var open = menu.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.innerHTML = open ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-bars"></i>';
      });
      menu.querySelectorAll('a').forEach(function(link){
        link.addEventListener('click', function(){
          menu.classList.remove('open');
          toggle.setAttribute('aria-expanded', 'false');
          toggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
        });
      });
    })();
  </script>
</body>
</html>
