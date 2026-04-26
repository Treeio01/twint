<?php ?><!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TWINT — Create Without Limits</title>
  <meta name="description"
    content="TWINT is the collaborative canvas where creative teams build, iterate, and ship extraordinary work together.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Syne:wght@700;800&display=swap"
    rel="stylesheet">
  <style>
    *,
    *::before,
    *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --bg: #060609;
      --surface: rgba(255, 255, 255, 0.04);
      --border: rgba(255, 255, 255, 0.08);
      --text: #e8e8f0;
      --muted: rgba(232, 232, 240, 0.45);
      --accent: #7c6fff;
      --accent2: #ff6fd8;
      --accent3: #6fffd1;
      --radius: 16px;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Inter', sans-serif;
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* ─── CANVAS ─── */
    #bg-canvas {
      position: fixed;
      inset: 0;
      z-index: 0;
      pointer-events: none;
    }

    /* ─── NAV ─── */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 20px 60px;
      background: rgba(6, 6, 9, 0.6);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--border);
      transition: padding .3s;
    }

    .logo {
      font-family: 'Syne', sans-serif;
      font-size: 22px;
      font-weight: 800;
      letter-spacing: -0.5px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-links {
      display: flex;
      gap: 36px;
      list-style: none;
    }

    .nav-links a {
      color: var(--muted);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: color .2s;
    }

    .nav-links a:hover {
      color: var(--text);
    }


    /* ─── HERO ─── */
    .hero {
      position: relative;
      z-index: 1;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 140px 24px 80px;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(124, 111, 255, 0.12);
      border: 1px solid rgba(124, 111, 255, 0.25);
      border-radius: 100px;
      padding: 6px 16px;
      font-size: 12px;
      font-weight: 600;
      color: var(--accent);
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-bottom: 32px;
    }

    .hero-badge::before {
      content: '';
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--accent);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
        transform: scale(1);
      }

      50% {
        opacity: 0.4;
        transform: scale(1.4);
      }
    }

    .hero h1 {
      font-family: 'Syne', sans-serif;
      font-size: clamp(48px, 8vw, 96px);
      font-weight: 800;
      line-height: 1.02;
      letter-spacing: -2px;
      max-width: 900px;
      margin-bottom: 24px;
    }

    .hero h1 .grad {
      background: linear-gradient(135deg, #fff 0%, var(--accent) 40%, var(--accent2) 80%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero p {
      font-size: 18px;
      color: var(--muted);
      max-width: 520px;
      margin: 0 auto;
      line-height: 1.7;
    }

    /* ─── MARQUEE ─── */
    .marquee-wrap {
      position: relative;
      z-index: 1;
      overflow: hidden;
      padding: 20px 0;
      border-top: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      background: rgba(255, 255, 255, 0.02);
    }

    .marquee-track {
      display: flex;
      gap: 60px;
      width: max-content;
      animation: marquee 30s linear infinite;
    }

    .marquee-track span {
      font-size: 13px;
      font-weight: 500;
      color: var(--muted);
      white-space: nowrap;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .marquee-dot {
      color: var(--accent) !important;
      font-size: 18px !important;
      line-height: 1;
      display: inline-block;
      vertical-align: middle;
    }

    @keyframes marquee {
      from {
        transform: translateX(0);
      }

      to {
        transform: translateX(-50%);
      }
    }

    /* ─── SECTIONS ─── */
    section {
      position: relative;
      z-index: 1;
    }

    .container {
      max-width: 1140px;
      margin: 0 auto;
      padding: 0 24px;
    }

    .section-label {
      display: inline-block;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--accent);
      margin-bottom: 16px;
    }

    .section-title {
      font-family: 'Syne', sans-serif;
      font-size: clamp(32px, 5vw, 52px);
      font-weight: 800;
      line-height: 1.1;
      letter-spacing: -1px;
      margin-bottom: 16px;
    }

    .section-desc {
      color: var(--muted);
      font-size: 17px;
      max-width: 500px;
      line-height: 1.7;
    }

    /* ─── FEATURES ─── */
    .features {
      padding: 120px 0;
    }

    .features-header {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: end;
      margin-bottom: 80px;
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .feature-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 32px;
      transition: border-color .3s, background .3s, transform .3s;
      position: relative;
      overflow: hidden;
      opacity: 0;
      transform: translateY(30px);
    }

    .feature-card.visible {
      animation: fadeUp .6s ease forwards;
    }

    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .feature-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at top left, var(--card-glow, rgba(124, 111, 255, 0.08)), transparent 60%);
      opacity: 0;
      transition: opacity .3s;
    }

    .feature-card:hover::before {
      opacity: 1;
    }

    .feature-card:hover {
      border-color: rgba(255, 255, 255, 0.14);
      transform: translateY(-4px);
    }

    .feature-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      margin-bottom: 20px;
    }

    .feature-card h3 {
      font-size: 17px;
      font-weight: 600;
      margin-bottom: 10px;
      letter-spacing: -0.3px;
    }

    .feature-card p {
      font-size: 14px;
      color: var(--muted);
      line-height: 1.65;
    }

    /* ─── SHOWCASE ─── */
    .showcase {
      padding: 80px 0 120px;
    }

    .showcase-inner {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 24px;
      overflow: hidden;
      position: relative;
    }

    .showcase-header {
      padding: 28px 36px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }

    .dot-red {
      background: #ff5f5f;
    }

    .dot-yellow {
      background: #ffbd2e;
    }

    .dot-green {
      background: #27c93f;
    }

    .showcase-body {
      padding: 48px 36px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      align-items: center;
    }

    .showcase-text .section-title {
      font-size: clamp(26px, 4vw, 40px);
    }

    .showcase-visual {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 24px;
      min-height: 280px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      position: relative;
      overflow: hidden;
    }

    .fake-bar {
      height: 10px;
      border-radius: 100px;
      background: var(--border);
      position: relative;
      overflow: hidden;
    }

    .fake-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      border-radius: inherit;
      animation: fillBar 2.4s ease-in-out infinite alternate;
    }

    .fb1::after {
      background: linear-gradient(90deg, var(--accent), var(--accent2));
      width: 72%;
      animation-delay: 0s;
    }

    .fb2::after {
      background: linear-gradient(90deg, var(--accent3), var(--accent));
      width: 55%;
      animation-delay: .3s;
    }

    .fb3::after {
      background: linear-gradient(90deg, var(--accent2), var(--accent3));
      width: 88%;
      animation-delay: .6s;
    }

    .fb4::after {
      background: linear-gradient(90deg, var(--accent), var(--accent3));
      width: 40%;
      animation-delay: .9s;
    }

    @keyframes fillBar {
      from {
        transform: scaleX(0.4);
        transform-origin: left;
      }

      to {
        transform: scaleX(1);
        transform-origin: left;
      }
    }

    .fake-tag {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(124, 111, 255, 0.12);
      border: 1px solid rgba(124, 111, 255, 0.2);
      border-radius: 6px;
      padding: 5px 10px;
      font-size: 12px;
      font-weight: 500;
      color: var(--accent);
    }

    .fake-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 8px;
    }

    /* ─── STATS ─── */
    .stats {
      padding: 80px 0 120px;
    }

    .stats-header {
      text-align: center;
      margin-bottom: 64px;
    }

    .stats-header .section-desc {
      margin: 0 auto;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 2px;
      background: var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      margin-bottom: 48px;
    }

    .stat-cell {
      background: var(--bg);
      padding: 40px 32px;
      text-align: center;
      opacity: 0;
      transform: translateY(20px);
    }

    .stat-cell.visible {
      animation: fadeUp .5s ease forwards;
    }

    .stat-num {
      font-family: 'Syne', sans-serif;
      font-size: clamp(36px, 5vw, 56px);
      font-weight: 800;
      letter-spacing: -2px;
      line-height: 1;
      background: linear-gradient(135deg, #fff, var(--accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 8px;
    }

    .stat-label {
      font-size: 13px;
      color: var(--muted);
      font-weight: 500;
    }

    /* ─── INTEGRATIONS ─── */
    .integrations-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
    }

    .int-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 24px 20px;
      display: flex;
      align-items: center;
      gap: 14px;
      transition: border-color .25s, transform .25s;
      opacity: 0;
      transform: translateY(20px);
    }

    .int-card.visible {
      animation: fadeUp .45s ease forwards;
    }

    .int-card:hover {
      border-color: rgba(255, 255, 255, 0.16);
      transform: translateY(-3px);
    }

    .int-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }

    .int-name {
      font-size: 14px;
      font-weight: 600;
      letter-spacing: -0.2px;
    }

    .int-type {
      font-size: 11px;
      color: var(--muted);
      margin-top: 2px;
    }


    /* ─── TESTIMONIALS ─── */
    .testimonials {
      padding: 60px 0 120px;
    }

    .testimonials-header {
      text-align: center;
      margin-bottom: 56px;
    }

    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .tcard {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 28px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      opacity: 0;
      transform: translateY(20px);
    }

    .tcard.visible {
      animation: fadeUp .5s ease forwards;
    }

    .stars {
      color: #ffbd2e;
      font-size: 14px;
      letter-spacing: 2px;
    }

    .tcard-text {
      font-size: 14px;
      color: var(--muted);
      line-height: 1.7;
      flex: 1;
    }

    .tcard-author {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
    }

    .tcard-name {
      font-size: 14px;
      font-weight: 600;
    }

    .tcard-role {
      font-size: 12px;
      color: var(--muted);
    }


    /* ─── FOOTER ─── */
    footer {
      position: relative;
      z-index: 1;
      border-top: 1px solid var(--border);
      padding: 40px 60px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
    }

    .footer-links {
      display: flex;
      gap: 28px;
      list-style: none;
    }

    .footer-links a {
      color: var(--muted);
      text-decoration: none;
      font-size: 13px;
      transition: color .2s;
    }

    .footer-links a:hover {
      color: var(--text);
    }

    .footer-copy {
      font-size: 13px;
      color: var(--muted);
    }

    /* ─── RESPONSIVE ─── */
    @media (max-width: 900px) {
      nav {
        padding: 16px 24px;
      }

      .nav-links {
        display: none;
      }

      .features-header {
        grid-template-columns: 1fr;
        gap: 24px;
      }

      .features-grid {
        grid-template-columns: 1fr 1fr;
      }

      .showcase-body {
        grid-template-columns: 1fr;
      }

      .stats-row {
        grid-template-columns: 1fr 1fr;
      }

      .integrations-grid {
        grid-template-columns: 1fr 1fr;
      }

      .testimonials-grid {
        grid-template-columns: 1fr;
      }

      .cta-inner {
        padding: 48px 24px;
      }

      footer {
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 32px 24px;
      }
    }

    @media (max-width: 600px) {
      .features-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>

  <canvas id="bg-canvas"></canvas>

  <!-- NAV -->
  <nav>
    <div class="logo">TWINT</div>
    <ul class="nav-links">
      <li><a href="#features">Features</a></li>
      <li><a href="#showcase">How it works</a></li>
      <li><a href="#integrations">Integrations</a></li>
      <li><a href="#testimonials">Reviews</a></li>
    </ul>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-badge">Now in public beta</div>
    <h1><span class="grad">Creative work,<br>finally organized.</span></h1>
    <p>TWINT is the collaborative workspace where design teams ideate, iterate, and ship — all without switching tabs.</p>
  </section>

  <!-- MARQUEE -->
  <div class="marquee-wrap" aria-hidden="true">
    <div class="marquee-track">
      <?php $items = ['Figma integration', 'Real-time collaboration', 'Version history', 'Asset library', 'Design tokens', 'Component sync', 'Live cursors', 'Smart layouts', 'Auto-export', 'Brand kits', 'Annotation tools', 'Handoff specs', 'Figma integration', 'Real-time collaboration', 'Version history', 'Asset library', 'Design tokens', 'Component sync', 'Live cursors', 'Smart layouts', 'Auto-export', 'Brand kits', 'Annotation tools', 'Handoff specs'];
      foreach ($items as $i => $item): ?>
        <span><?= htmlspecialchars($item) ?></span><?php if ($i < count($items) - 1): ?><span
            class="marquee-dot">·</span><?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- FEATURES -->
  <section class="features" id="features">
    <div class="container">
      <div class="features-header">
        <div>
          <span class="section-label">Features</span>
          <h2 class="section-title">Everything your<br>team needs</h2>
        </div>
        <p class="section-desc">Built for the speed of modern creative workflows. No bloat, no friction — just the tools
          that matter.</p>
      </div>

      <div class="features-grid">
        <?php
        $features = [
          ['icon' => '⚡', 'color' => 'rgba(124,111,255,0.15)', 'glow' => 'rgba(124,111,255,0.1)', 'title' => 'Instant sync', 'desc' => 'Changes appear in real time across every device. No manual refreshes, no version conflicts.'],
          ['icon' => '🎨', 'color' => 'rgba(255,111,216,0.12)', 'glow' => 'rgba(255,111,216,0.08)', 'title' => 'Token-based theming', 'desc' => 'Define once, apply everywhere. Design tokens cascade through your entire component library automatically.'],
          ['icon' => '🔗', 'color' => 'rgba(111,255,209,0.12)', 'glow' => 'rgba(111,255,209,0.08)', 'title' => 'Figma bridge', 'desc' => 'Two-way sync with Figma. Push updates from TWINT, pull changes back — no copy-paste hand-offs.'],
          ['icon' => '📦', 'color' => 'rgba(255,189,46,0.1)', 'glow' => 'rgba(255,189,46,0.07)', 'title' => 'Shared asset library', 'desc' => 'One source of truth for icons, illustrations, fonts, and motion specs. Always up-to-date for everyone.'],
          ['icon' => '🧩', 'color' => 'rgba(124,111,255,0.15)', 'glow' => 'rgba(124,111,255,0.1)', 'title' => 'Component docs', 'desc' => 'Auto-generate documentation from your components. Developers get specs without asking designers.'],
          ['icon' => '📊', 'color' => 'rgba(255,111,216,0.12)', 'glow' => 'rgba(255,111,216,0.08)', 'title' => 'Usage analytics', 'desc' => 'See which components ship most, who uses what, and where inconsistencies sneak in.'],
        ];
        foreach ($features as $i => $f): ?>
          <div class="feature-card" style="--card-glow:<?= $f['glow'] ?>; animation-delay:<?= $i * 0.08 ?>s">
            <div class="feature-icon" style="background:<?= $f['color'] ?>"><?= $f['icon'] ?></div>
            <h3><?= $f['title'] ?></h3>
            <p><?= $f['desc'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- SHOWCASE -->
  <section class="showcase" id="showcase">
    <div class="container">
      <div class="showcase-inner">
        <div class="showcase-header">
          <div class="dot dot-red"></div>
          <div class="dot dot-yellow"></div>
          <div class="dot dot-green"></div>
          <span style="font-size:13px;color:var(--muted);margin-left:8px;">TWINT workspace — Acme Design System
            v3.2</span>
        </div>
        <div class="showcase-body">
          <div class="showcase-text">
            <span class="section-label">How it works</span>
            <h2 class="section-title">Your design system,<br>alive and breathing</h2>
            <p style="color:var(--muted);font-size:15px;line-height:1.7;margin-top:12px;">TWINT
              tracks your component health in real time. See coverage, adoption rate, and drift — before they become
              technical debt.</p>
          </div>
          <div class="showcase-visual">
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;font-weight:500">Component coverage</div>
            <div class="fake-bar fb1"></div>
            <div class="fake-bar fb2"></div>
            <div class="fake-bar fb3"></div>
            <div class="fake-bar fb4"></div>
            <div class="fake-tags">
              <?php foreach (['Button', 'Card', 'Modal', 'Input', 'Badge', 'Tooltip', 'Dropdown'] as $tag): ?>
                <span class="fake-tag"><?= $tag ?></span>
              <?php endforeach; ?>
            </div>
            <div
              style="margin-top:auto;padding-top:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:12px;color:var(--muted)">
              <span>87% coverage</span>
              <span style="color:var(--accent3)">↑ 12% this week</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- STATS + INTEGRATIONS -->
  <section class="stats" id="integrations">
    <div class="container">
      <div class="stats-header">
        <span class="section-label">By the numbers</span>
        <h2 class="section-title">Built for scale,<br>loved by teams</h2>
        <p class="section-desc">From solo designers to enterprise orgs — TWINT grows with your workflow.</p>
      </div>

      <div class="stats-row">
        <?php
        $stats = [
          ['num' => '12k+',  'label' => 'Active designers'],
          ['num' => '340k',  'label' => 'Components synced'],
          ['num' => '98%',   'label' => 'Uptime SLA'],
          ['num' => '4.9★',  'label' => 'Average rating'],
        ];
        foreach ($stats as $i => $s): ?>
          <div class="stat-cell" style="animation-delay:<?= $i * 0.08 ?>s">
            <div class="stat-num"><?= $s['num'] ?></div>
            <div class="stat-label"><?= $s['label'] ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="integrations-grid">
        <?php
        $integrations = [
          ['icon' => '🎨', 'bg' => 'rgba(162,89,255,0.15)', 'name' => 'Figma',    'type' => 'Design tool'],
          ['icon' => '💬', 'bg' => 'rgba(74,21,75,0.3)',    'name' => 'Slack',    'type' => 'Communication'],
          ['icon' => '🐙', 'bg' => 'rgba(255,255,255,0.06)','name' => 'GitHub',   'type' => 'Version control'],
          ['icon' => '📋', 'bg' => 'rgba(255,255,255,0.06)','name' => 'Notion',   'type' => 'Documentation'],
          ['icon' => '🔷', 'bg' => 'rgba(30,100,255,0.12)', 'name' => 'Linear',   'type' => 'Issue tracking'],
          ['icon' => '🔴', 'bg' => 'rgba(255,60,60,0.1)',   'name' => 'Jira',     'type' => 'Project management'],
          ['icon' => '💻', 'bg' => 'rgba(0,120,212,0.12)',  'name' => 'VS Code',  'type' => 'Code editor'],
          ['icon' => '🟠', 'bg' => 'rgba(255,138,0,0.12)',  'name' => 'Zeplin',   'type' => 'Handoff'],
          ['icon' => '🌐', 'bg' => 'rgba(111,255,209,0.1)', 'name' => 'Storybook','type' => 'Component docs'],
          ['icon' => '📊', 'bg' => 'rgba(255,111,216,0.1)', 'name' => 'Mixpanel', 'type' => 'Analytics'],
          ['icon' => '☁️', 'bg' => 'rgba(60,160,255,0.1)',  'name' => 'AWS S3',   'type' => 'Asset storage'],
          ['icon' => '🔔', 'bg' => 'rgba(124,111,255,0.15)','name' => 'Zapier',   'type' => 'Automation'],
        ];
        foreach ($integrations as $i => $int): ?>
          <div class="int-card" style="animation-delay:<?= $i * 0.05 ?>s">
            <div class="int-icon" style="background:<?= $int['bg'] ?>"><?= $int['icon'] ?></div>
            <div>
              <div class="int-name"><?= $int['name'] ?></div>
              <div class="int-type"><?= $int['type'] ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="testimonials" id="testimonials">
    <div class="container">
      <div class="testimonials-header">
        <span class="section-label">Reviews</span>
        <h2 class="section-title">Teams love TWINT</h2>
      </div>
      <div class="testimonials-grid">
        <?php
        $reviews = [
          ['stars' => 5, 'text' => 'We cut our design-to-dev handoff time by 60%. The Figma bridge is a game-changer — no more "wait, which file is the latest?"', 'name' => 'Sarah K.', 'role' => 'Head of Design, Luma Studio', 'color' => '#7c6fff', 'init' => 'SK'],
          ['stars' => 5, 'text' => 'Finally, a tool that understands design systems aren\'t just Figma files. The token sync alone is worth every penny.', 'name' => 'Marcus R.', 'role' => 'Design System Lead, Forma', 'color' => '#ff6fd8', 'init' => 'MR'],
          ['stars' => 5, 'text' => 'Our component coverage went from 40% to 91% in two sprints. The analytics dashboard made the invisible, visible.', 'name' => 'Priya M.', 'role' => 'Product Designer, Velox', 'color' => '#6fffd1', 'init' => 'PM'],
        ];
        foreach ($reviews as $i => $r): ?>
          <div class="tcard" style="animation-delay:<?= $i * 0.1 ?>s">
            <div class="stars"><?= str_repeat('★', $r['stars']) ?></div>
            <p class="tcard-text">"<?= htmlspecialchars($r['text']) ?>"</p>
            <div class="tcard-author">
              <div class="avatar" style="background:<?= $r['color'] ?>;opacity:0.85"><?= $r['init'] ?></div>
              <div>
                <div class="tcard-name"><?= htmlspecialchars($r['name']) ?></div>
                <div class="tcard-role"><?= htmlspecialchars($r['role']) ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="logo">TWINT</div>
    <ul class="footer-links">
      <li><a href="#">Privacy</a></li>
      <li><a href="#">Terms</a></li>
      <li><a href="#">Security</a></li>
      <li><a href="#">Status</a></li>
      <li><a href="#">Blog</a></li>
    </ul>
    <div class="footer-copy">© <?= date('Y') ?> TWINT Inc. All rights reserved.</div>
  </footer>

  <script>
    /* ─── CANVAS PARTICLES ─── */
    (function () {
      const canvas = document.getElementById('bg-canvas');
      const ctx = canvas.getContext('2d');
      let W, H, particles = [], lines = [];
      const COUNT = 80;
      const COLORS = ['rgba(124,111,255,', 'rgba(255,111,216,', 'rgba(111,255,209,'];

      function resize() {
        W = canvas.width = window.innerWidth;
        H = canvas.height = window.innerHeight;
      }

      function randBetween(a, b) { return a + Math.random() * (b - a); }

      function createParticle() {
        const c = COLORS[Math.floor(Math.random() * COLORS.length)];
        return {
          x: Math.random() * W,
          y: Math.random() * H,
          r: randBetween(1, 2.5),
          vx: randBetween(-0.2, 0.2),
          vy: randBetween(-0.15, 0.15),
          color: c,
          alpha: randBetween(0.3, 0.7),
        };
      }

      function init() {
        particles = Array.from({ length: COUNT }, createParticle);
      }

      function draw() {
        ctx.clearRect(0, 0, W, H);

        // draw connections
        for (let i = 0; i < particles.length; i++) {
          for (let j = i + 1; j < particles.length; j++) {
            const dx = particles[i].x - particles[j].x;
            const dy = particles[i].y - particles[j].y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < 140) {
              const a = (1 - dist / 140) * 0.12;
              ctx.beginPath();
              ctx.moveTo(particles[i].x, particles[i].y);
              ctx.lineTo(particles[j].x, particles[j].y);
              ctx.strokeStyle = `rgba(124,111,255,${a})`;
              ctx.lineWidth = 1;
              ctx.stroke();
            }
          }
        }

        // draw particles
        for (const p of particles) {
          ctx.beginPath();
          ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
          ctx.fillStyle = p.color + p.alpha + ')';
          ctx.fill();

          p.x += p.vx;
          p.y += p.vy;
          if (p.x < -10) p.x = W + 10;
          if (p.x > W + 10) p.x = -10;
          if (p.y < -10) p.y = H + 10;
          if (p.y > H + 10) p.y = -10;
        }
      }

      function loop() { draw(); requestAnimationFrame(loop); }

      window.addEventListener('resize', () => { resize(); });
      resize();
      init();
      loop();
    })();

    /* ─── SCROLL ANIMATIONS ─── */
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('visible');
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.feature-card, .stat-cell, .int-card, .tcard').forEach(el => observer.observe(el));

    /* ─── NAV SHRINK ─── */
    window.addEventListener('scroll', () => {
      const nav = document.querySelector('nav');
      nav.style.padding = window.scrollY > 40 ? '14px 60px' : '20px 60px';
    });
  </script>
</body>

</html>