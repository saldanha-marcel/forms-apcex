<?php
// Carregar configurações do .env
$config = parse_ini_file('.env');

// Conectar ao PostgreSQL
try {
    $pdo = new PDO(
        "pgsql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']}",
        $config['DB_USER'],
        $config['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Processar o formulário
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletar dados
    $genero = $_POST['genero'] ?? '';
    $idade = $_POST['idade'] ?? '';
    $ticket = (int)($_POST['ticket'] ?? 150);
    $fv = $_POST['fv'] ?? '';
    $fc = $_POST['fc'] ?? '';
    $fg = $_POST['fg'] ?? '';
    $forma = isset($_POST['forma']) ? json_encode($_POST['forma']) : '[]';
    $atrai = isset($_POST['atrai']) ? json_encode($_POST['atrai']) : '[]';
    $produtos = isset($_POST['produtos']) ? json_encode($_POST['produtos']) : '[]';
    $motivo = isset($_POST['motivo']) ? json_encode($_POST['motivo']) : '[]';
    $escolha = $_POST['escolha'] ?? '';
    $dor = $_POST['dor'] ?? '';
    $nome = $_POST['nome'] ?? '';

    // Inserir no banco
    $stmt = $pdo->prepare("
        INSERT INTO personas (genero, idade, ticket, fv, fc, fg, forma, atrai, produtos, motivo, escolha, dor, nome, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$genero, $idade, $ticket, $fv, $fc, $fg, $forma, $atrai, $produtos, $motivo, $escolha, $dor, $nome]);

    $message = 'Persona salva com sucesso!';
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Personas — ApcéX</title>
<link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@300;400;500;600;700;800&family=Nunito:wght@300;400;500;600&family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --navy:    #0A0E6B;
    --navy2:   #0D1280;
    --blue:    #1218A8;
    --purple:  #5B2CB0;
    --purple2: #7B3FD4;
    --gold:    #F5B800;
    --gold2:   #FFD04D;
    --white:   #FFFFFF;
    --white80: rgba(255,255,255,0.80);
    --white40: rgba(255,255,255,0.40);
    --white15: rgba(255,255,255,0.08);
    --white08: rgba(255,255,255,0.05);
    --glow-purple: rgba(91,44,176,0.45);
    --glow-gold:   rgba(245,184,0,0.35);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  html { scroll-behavior: smooth; }

  body {
    font-family: 'Roboto', sans-serif;
    background: #FAFAFA;
    color: #333;
    min-height: 100vh;
    overflow-x: hidden;
    position: relative;
  }

  /* ── Background atmosphere ── */
  body::before {
    content: '';
    position: fixed; inset: 0; z-index: 0;
    background: transparent;
    pointer-events: none;
  }

  /* grid lines */
  body::after {
    content: '';
    position: fixed; inset: 0; z-index: 0;
    background-image:
      linear-gradient(rgba(200,200,200,0.05) 1px, transparent 1px),
      linear-gradient(90deg, rgba(200,200,200,0.05) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
  }

  /* ── HEADER ── */
  .header {
    position: relative; z-index: 10;
    padding: 48px 40px 36px;
    text-align: center;
    border-bottom: 1px solid #E0E0E0;
    overflow: hidden;
    background: #FFFFFF;
  }
  .header-glow {
    position: absolute;
    top: -60px; left: 50%; transform: translateX(-50%);
    width: 500px; height: 200px;
    background: transparent;
    pointer-events: none;
  }

  /* Logo text */
  .logo {
    font-family: 'Exo 2', sans-serif;
    font-size: 48px;
    font-weight: 800;
    letter-spacing: -1px;
    line-height: 1;
    margin-bottom: 12px;
    position: relative;
    display: inline-block;
  }
  .logo .apc { color: #1A1A1A; }
  .logo .e   { color: #1A1A1A; }
  .logo .x {
    color: var(--purple2);
    position: relative;
    display: inline-block;
  }
  .logo .x::after {
    content: '';
    position: absolute;
    right: -28px; top: 2px;
    width: 22px; height: 22px;
    background: var(--gold);
    clip-path: polygon(0 100%, 30% 40%, 50% 100%, 55% 60%, 100% 0%, 70% 15%, 65% 0%, 35% 55%, 10% 10%);
    /* arrow shape via CSS */
  }

  /* SVG logo inline baseado na identidade ApcéX */
  .logo-svg {
    display: block;
    margin: 0 auto 14px;
    height: 72px;
    width: auto;
  }

  .header-subtitle {
    font-size: 13px;
    color: #888;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 500;
    margin-bottom: 6px;
  }
  .header-title {
    font-family: 'Exo 2', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #1A1A1A;
    margin-bottom: 8px;
  }
  .header-desc {
    font-size: 14px;
    color: #666;
    max-width: 440px;
    margin: 0 auto;
    line-height: 1.6;
  }
  .gold-line {
    width: 60px; height: 3px;
    background: linear-gradient(90deg, var(--gold), var(--purple2));
    border-radius: 2px;
    margin: 18px auto 0;
  }

  /* ── PROGRESS ── */
  .progress-wrap {
    position: sticky; top: 0; z-index: 50;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #E0E0E0;
    padding: 12px 40px;
    display: flex; align-items: center; gap: 14px;
  }
  .prog-steps { display: flex; gap: 8px; }
  .prog-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #E8E8E8;
    border: 1px solid #D0D0D0;
    transition: all 0.3s;
  }
  .prog-dot.active {
    background: var(--gold);
    border-color: var(--gold);
    box-shadow: 0 0 8px var(--gold);
  }
  .prog-track {
    flex: 1; height: 3px;
    background: #E8E8E8;
    border-radius: 2px; overflow: hidden;
  }
  .prog-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--purple2), var(--gold));
    border-radius: 2px;
    transition: width 0.5s cubic-bezier(.4,0,.2,1);
    width: 0%;
    position: relative;
  }
  .prog-fill::after {
    content: '';
    position: absolute; right: 0; top: -3px;
    width: 9px; height: 9px;
    background: var(--gold);
    border-radius: 50%;
    box-shadow: 0 0 8px var(--gold);
  }
  .prog-label {
    font-family: 'Exo 2', sans-serif;
    font-size: 11px; font-weight: 600;
    color: #888;
    white-space: nowrap;
    letter-spacing: 1px;
  }

  /* ── MAIN ── */
  .main {
    position: relative; z-index: 5;
    max-width: 700px;
    margin: 0 auto;
    padding: 44px 20px 80px;
  }

  /* ── SECTION CARD ── */
  .card {
    background: #FFFFFF;
    border: 1px solid #E8E8E8;
    border-radius: 20px;
    margin-bottom: 20px;
    overflow: hidden;
    backdrop-filter: blur(4px);
    transition: border-color 0.3s, box-shadow 0.3s;
    opacity: 0;
    transform: translateY(24px);
    animation: slideUp 0.55s cubic-bezier(.4,0,.2,1) forwards;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }
  .card:hover {
    border-color: rgba(91,44,176,0.3);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  }
  .card:nth-child(1) { animation-delay: 0.08s; }
  .card:nth-child(2) { animation-delay: 0.16s; }
  .card:nth-child(3) { animation-delay: 0.24s; }
  .card:nth-child(4) { animation-delay: 0.32s; }
  .card:nth-child(5) { animation-delay: 0.40s; }

  @keyframes slideUp {
    to { opacity: 1; transform: translateY(0); }
  }

  .card-header {
    padding: 22px 26px 18px;
    background: linear-gradient(135deg, rgba(91,44,176,0.08) 0%, rgba(245,184,0,0.04) 100%);
    border-bottom: 1px solid #F0F0F0;
    display: flex; align-items: center; gap: 16px;
  }
  .card-num {
    font-family: 'Exo 2', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 2px;
    color: var(--gold);
    background: rgba(245,184,0,0.12);
    border: 1px solid rgba(245,184,0,0.25);
    border-radius: 6px;
    padding: 3px 9px;
    white-space: nowrap;
  }
  .card-icon {
    font-size: 22px; line-height: 1;
  }
  .card-info h2 {
    font-family: 'Exo 2', sans-serif;
    font-size: 16px; font-weight: 700;
    color: #1A1A1A;
    letter-spacing: 0.3px;
  }
  .card-info p {
    font-size: 12px;
    color: #888;
    margin-top: 2px;
  }

  .card-body {
    padding: 26px;
    display: flex; flex-direction: column; gap: 22px;
  }

  /* ── FIELD ── */
  .field { display: flex; flex-direction: column; gap: 9px; }
  .field > label {
    font-size: 13px; font-weight: 600;
    color: #333;
    display: flex; align-items: center; gap: 7px;
  }
  .req { color: var(--gold); font-size: 10px; }

  /* ── CHIP (radio/checkbox) ── */
  .chips { display: flex; flex-wrap: wrap; gap: 9px; }
  .chip input { display: none; }
  .chip label {
    display: flex; align-items: center; gap: 7px;
    padding: 8px 16px;
    border: 1.5px solid #D8D8D8;
    border-radius: 100px;
    font-size: 13px; font-weight: 500;
    color: #666;
    cursor: pointer;
    transition: all 0.2s;
    background: #F8F8F8;
    user-select: none;
  }
  .chip label:hover {
    border-color: var(--purple2);
    color: #333;
    background: rgba(91,44,176,0.08);
  }
  .chip input:checked + label {
    border-color: var(--gold);
    color: var(--navy);
    background: var(--gold);
    font-weight: 700;
    box-shadow: 0 0 16px rgba(245,184,0,0.3);
  }

  /* ── FREQ ROW ── */
  .freq-group { display: flex; flex-direction: column; gap: 8px; }
  .freq-row {
    display: flex; align-items: center; gap: 14px;
    padding: 11px 16px;
    border-radius: 12px;
    background: #F8F8F8;
    border: 1px solid #E8E8E8;
    transition: border-color 0.2s;
  }
  .freq-row:hover { border-color: rgba(91,44,176,0.3); }
  .freq-name {
    font-size: 13px; font-weight: 600;
    color: #333;
    min-width: 110px;
    display: flex; align-items: center; gap: 8px;
  }
  .freq-options { display: flex; gap: 7px; flex-wrap: wrap; }
  .freq-opt input { display: none; }
  .freq-opt label {
    padding: 5px 13px;
    border-radius: 8px;
    font-size: 12px; font-weight: 600;
    border: 1.5px solid #D8D8D8;
    color: #666;
    cursor: pointer;
    transition: all 0.18s;
    background: transparent;
  }
  .freq-opt input:checked + label {
    background: var(--purple2);
    border-color: var(--purple2);
    color: white;
    box-shadow: 0 0 12px rgba(123,63,212,0.4);
  }
  .freq-opt label:hover { border-color: var(--purple2); color: #333; }

  /* ── SLIDER ── */
  .range-wrap { display: flex; flex-direction: column; gap: 8px; }
  .range-val {
    font-family: 'Exo 2', sans-serif;
    font-size: 26px; font-weight: 800;
    background: linear-gradient(90deg, var(--gold), var(--purple2));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  input[type="range"] {
    -webkit-appearance: none;
    width: 100%; height: 4px;
    background: rgba(200,200,200,0.25);
    border-radius: 2px; outline: none;
    padding: 0; border: none; box-shadow: none;
  }
  input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px; height: 24px;
    background: radial-gradient(circle, var(--gold2), var(--gold));
    border-radius: 50%;
    cursor: pointer;
    border: 3px solid var(--navy);
    box-shadow: 0 0 12px rgba(245,184,0,0.5);
    transition: transform 0.15s;
  }
  input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.25); }
  .range-labels { display: flex; justify-content: space-between; font-size: 11px; color: var(--white40); }

  /* ── INPUTS / TEXTAREA ── */
  input[type="text"], input[type="number"], select, textarea {
    font-family: 'Roboto', sans-serif;
    font-size: 14px; font-weight: 500;
    padding: 12px 16px;
    background: #F8F8F8;
    border: 1.5px solid #E0E0E0;
    border-radius: 12px;
    color: #333;
    outline: none;
    width: 100%;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  input::placeholder, textarea::placeholder { color: rgba(100,100,100,0.5); }
  input:focus, textarea:focus {
    border-color: var(--purple2);
    box-shadow: 0 0 0 3px rgba(123,63,212,0.1);
    background: #FFFFFF;
  }
  textarea { resize: vertical; min-height: 90px; }

  /* field-row */
  .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  @media (max-width: 520px) { .field-row { grid-template-columns: 1fr; } }

  /* ── SUBMIT ── */
  .submit-card {
    background: linear-gradient(135deg, rgba(91,44,176,0.08), rgba(245,184,0,0.05));
    border: 1px solid rgba(245,184,0,0.3);
    border-radius: 20px;
    padding: 32px 26px;
    text-align: center;
    opacity: 0; transform: translateY(24px);
    animation: slideUp 0.55s 0.48s cubic-bezier(.4,0,.2,1) forwards;
  }
  .btn-submit {
    width: 100%; padding: 18px;
    background: linear-gradient(135deg, var(--gold) 0%, #E6A800 100%);
    color: var(--navy);
    border: none;
    border-radius: 14px;
    font-family: 'Exo 2', sans-serif;
    font-size: 16px; font-weight: 800;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.25s;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    box-shadow: 0 6px 24px rgba(245,184,0,0.25);
  }
  .btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(245,184,0,0.4);
    background: linear-gradient(135deg, var(--gold2), var(--gold));
  }
  .btn-submit:active { transform: translateY(0); }
  .submit-note {
    font-size: 12px;
    color: #888;
    margin-top: 12px;
  }
  .powered {
    margin-top: 20px;
    font-size: 11px;
    color: rgba(100,100,100,0.5);
    letter-spacing: 2px;
    text-transform: uppercase;
  }
  .powered span {
    font-family: 'Exo 2', sans-serif;
    font-weight: 700;
    background: linear-gradient(90deg, var(--purple2), var(--gold));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  /* ── MODAL ── */
  .overlay {
    display: none; position: fixed; inset: 0; z-index: 999;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(10px);
    align-items: center; justify-content: center;
  }
  .overlay.show { display: flex; }
  .modal {
    background: linear-gradient(145deg, #FFFFFF, #F5F5F5);
    border: 1px solid #E0E0E0;
    border-radius: 24px;
    padding: 48px 40px;
    text-align: center;
    max-width: 380px; width: 90%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    animation: popIn 0.4s cubic-bezier(0.34,1.56,0.64,1);
  }
  @keyframes popIn {
    from { transform: scale(0.75); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
  }
  .modal-icon { font-size: 52px; margin-bottom: 16px; }
  .modal h3 {
    font-family: 'Exo 2', sans-serif;
    font-size: 22px; font-weight: 800;
    background: linear-gradient(90deg, #1A1A1A, var(--gold));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
  }
  .modal p { font-size: 14px; color: #666; line-height: 1.7; }
  .btn-close {
    margin-top: 24px;
    padding: 12px 32px;
    background: linear-gradient(135deg, var(--gold), #E6A800);
    color: var(--navy);
    border: none; border-radius: 100px;
    font-family: 'Exo 2', sans-serif;
    font-size: 14px; font-weight: 800;
    cursor: pointer;
    transition: all 0.2s;
    letter-spacing: 0.5px;
  }
  .btn-close:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(245,184,0,0.35); }

  /* Message */
  .message {
    position: fixed; top: 20px; right: 20px; z-index: 1000;
    background: var(--gold);
    color: var(--navy);
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(245,184,0,0.3);
    animation: slideIn 0.3s;
  }
  @keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
</style>
</head>
<body>

<?php if ($message): ?>
<div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<!-- HEADER -->
<div class="header">
  <div class="header-glow"></div>

  <!-- Logo SVG inline baseado na identidade ApcéX -->
  <svg class="logo-svg" viewBox="0 0 260 72" fill="none" xmlns="http://www.w3.org/2000/svg">
    <!-- "Apce" em branco -->
    <text x="0" y="60" font-family="'Exo 2', sans-serif" font-size="64" font-weight="800" fill="white" letter-spacing="-2">Apce</text>
    <!-- "X" em roxo -->
    <text x="172" y="60" font-family="'Exo 2', sans-serif" font-size="64" font-weight="800" fill="#7B3FD4" letter-spacing="-2">X</text>
    <!-- Seta dourada em cima do X -->
    <g transform="translate(204, 0)">
      <!-- corpo da seta -->
      <path d="M8 52 Q20 30 38 8" stroke="#F5B800" stroke-width="4" stroke-linecap="round" fill="none"/>
      <!-- ponta da seta -->
      <polygon points="38,8 28,14 34,22" fill="#F5B800"/>
    </g>
  </svg>

  <div class="header-subtitle">Desenvolvido por ApcéX</div>
  <div class="header-title">Mapeamento de Personas</div>
  <p class="header-desc">Preencha o perfil do cliente para construir personas estratégicas</p>
  <div class="gold-line"></div>
</div>

<!-- PROGRESS -->
<div class="progress-wrap">
  <div class="prog-steps" id="progDots">
    <div class="prog-dot" data-s="1"></div>
    <div class="prog-dot" data-s="2"></div>
    <div class="prog-dot" data-s="3"></div>
    <div class="prog-dot" data-s="4"></div>
    <div class="prog-dot" data-s="5"></div>
  </div>
  <div class="prog-track"><div class="prog-fill" id="progFill"></div></div>
  <div class="prog-label" id="progLabel">0 / 5</div>
</div>

<!-- MAIN -->
<div class="main">
<form id="mainForm" method="POST" onsubmit="handleSubmit(event)">

  <!-- 1. Características -->
  <div class="card">
    <div class="card-header">
      <span class="card-num">01</span>
      <span class="card-icon">👤</span>
      <div class="card-info">
        <h2>Características do cliente</h2>
        <p>Perfil demográfico básico</p>
      </div>
    </div>
    <div class="card-body">
      <div class="field-row">
        <div class="field">
          <label>Gênero <span class="req">★</span></label>
          <div class="chips">
            <div class="chip"><input type="radio" name="genero" id="gF" value="Feminino"><label for="gF">♀ Feminino</label></div>
            <div class="chip"><input type="radio" name="genero" id="gM" value="Masculino"><label for="gM">♂ Masculino</label></div>
            <div class="chip"><input type="radio" name="genero" id="gO" value="Outro"><label for="gO">✦ Outro</label></div>
          </div>
        </div>
        <div class="field">
          <label>Faixa etária <span class="req">★</span></label>
          <div class="chips">
            <div class="chip"><input type="radio" name="idade" id="i1" value="18-25"><label for="i1">18–25</label></div>
            <div class="chip"><input type="radio" name="idade" id="i2" value="26-35"><label for="i2">26–35</label></div>
            <div class="chip"><input type="radio" name="idade" id="i3" value="36-45"><label for="i3">36–45</label></div>
            <div class="chip"><input type="radio" name="idade" id="i4" value="46+"><label for="i4">46+</label></div>
          </div>
        </div>
      </div>
      <div class="field">
        <label>Ticket médio por compra</label>
        <div class="range-wrap">
          <div class="range-val" id="ticketVal">R$ 150</div>
          <input type="range" id="ticketSlider" name="ticket" min="50" max="1000" step="50" value="150"
            oninput="updateTicket(this.value)">
          <div class="range-labels"><span>R$ 50</span><span>R$ 500</span><span>R$ 1.000+</span></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. Relação com produtos gourmet -->
  <div class="card">
    <div class="card-header">
      <span class="card-num">02</span>
      <span class="card-icon">🍷</span>
      <div class="card-info">
        <h2>Relação com produtos gourmet</h2>
        <p>Frequência e contexto de consumo</p>
      </div>
    </div>
    <div class="card-body">
      <div class="field">
        <label>Com que frequência consome:</label>
        <div class="freq-group">
          <div class="freq-row">
            <span class="freq-name">🍷 Vinhos</span>
            <div class="freq-options">
              <div class="freq-opt"><input type="radio" name="fv" id="fv1" value="Raramente"><label for="fv1">Raramente</label></div>
              <div class="freq-opt"><input type="radio" name="fv" id="fv2" value="Mensal"><label for="fv2">Mensal</label></div>
              <div class="freq-opt"><input type="radio" name="fv" id="fv3" value="Semanal"><label for="fv3">Semanal</label></div>
            </div>
          </div>
          <div class="freq-row">
            <span class="freq-name">🍺 Cervejas</span>
            <div class="freq-options">
              <div class="freq-opt"><input type="radio" name="fc" id="fc1" value="Raramente"><label for="fc1">Raramente</label></div>
              <div class="freq-opt"><input type="radio" name="fc" id="fc2" value="Mensal"><label for="fc2">Mensal</label></div>
              <div class="freq-opt"><input type="radio" name="fc" id="fc3" value="Semanal"><label for="fc3">Semanal</label></div>
            </div>
          </div>
          <div class="freq-row">
            <span class="freq-name">🧀 Gourmet</span>
            <div class="freq-options">
              <div class="freq-opt"><input type="radio" name="fg" id="fg1" value="Raramente"><label for="fg1">Raramente</label></div>
              <div class="freq-opt"><input type="radio" name="fg" id="fg2" value="Mensal"><label for="fg2">Mensal</label></div>
              <div class="freq-opt"><input type="radio" name="fg" id="fg3" value="Semanal"><label for="fg3">Semanal</label></div>
            </div>
          </div>
        </div>
      </div>
      <div class="field">
        <label>Geralmente consome esses produtos de que forma?</label>
        <div class="chips">
          <div class="chip"><input type="checkbox" name="forma[]" id="fo1" value="Sozinho(a)"><label for="fo1">🧍 Sozinho(a)</label></div>
          <div class="chip"><input type="checkbox" name="forma[]" id="fo2" value="Com parceiro(a)"><label for="fo2">👫 Com parceiro(a)</label></div>
          <div class="chip"><input type="checkbox" name="forma[]" id="fo3" value="Com amigos"><label for="fo3">👥 Com amigos</label></div>
          <div class="chip"><input type="checkbox" name="forma[]" id="fo4" value="Eventos especiais"><label for="fo4">🥂 Eventos especiais</label></div>
          <div class="chip"><input type="checkbox" name="forma[]" id="fo5" value="Dia a dia"><label for="fo5">☀️ Dia a dia</label></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 3. Hábitos de compra -->
  <div class="card">
    <div class="card-header">
      <span class="card-num">03</span>
      <span class="card-icon">🛒</span>
      <div class="card-info">
        <h2>Hábitos de compra</h2>
        <p>O que atrai e o que é consumido na loja</p>
      </div>
    </div>
    <div class="card-body">
      <div class="field">
        <label>O que mais atrai em uma loja?</label>
        <div class="chips">
          <div class="chip"><input type="checkbox" name="atrai[]" id="a1" value="Preço"><label for="a1">💰 Preço</label></div>
          <div class="chip"><input type="checkbox" name="atrai[]" id="a2" value="Variedade"><label for="a2">📦 Variedade</label></div>
          <div class="chip"><input type="checkbox" name="atrai[]" id="a3" value="Atendimento"><label for="a3">🤝 Atendimento</label></div>
          <div class="chip"><input type="checkbox" name="atrai[]" id="a4" value="Experiência do ambiente"><label for="a4">✨ Experiência</label></div>
          <div class="chip"><input type="checkbox" name="atrai[]" id="a5" value="Exclusividade"><label for="a5">👑 Exclusividade</label></div>
        </div>
      </div>
      <div class="field">
        <label>Quais produtos consome na loja?</label>
        <div class="chips">
          <div class="chip"><input type="checkbox" name="produtos[]" id="p1" value="Vinhos"><label for="p1">🍷 Vinhos</label></div>
          <div class="chip"><input type="checkbox" name="produtos[]" id="p2" value="Cervejas"><label for="p2">🍺 Cervejas</label></div>
          <div class="chip"><input type="checkbox" name="produtos[]" id="p3" value="Alimentos"><label for="p3">🧀 Alimentos</label></div>
          <div class="chip"><input type="checkbox" name="produtos[]" id="p4" value="Livros"><label for="p4">📚 Livros</label></div>
          <div class="chip"><input type="checkbox" name="produtos[]" id="p5" value="Presentes"><label for="p5">🎁 Presentes</label></div>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. Motivações -->
  <div class="card">
    <div class="card-header">
      <span class="card-num">04</span>
      <span class="card-icon">🎯</span>
      <div class="card-info">
        <h2>Motivações de compra</h2>
        <p>Razões e critérios de decisão</p>
      </div>
    </div>
    <div class="card-body">
      <div class="field">
        <label>Por que compra vinhos/cervejas/produtos gourmet?</label>
        <div class="chips">
          <div class="chip"><input type="checkbox" name="motivo[]" id="mt1" value="Relaxar"><label for="mt1">😌 Relaxar</label></div>
          <div class="chip"><input type="checkbox" name="motivo[]" id="mt2" value="Presentear"><label for="mt2">🎁 Presentear</label></div>
          <div class="chip"><input type="checkbox" name="motivo[]" id="mt3" value="Harmonizar com refeições"><label for="mt3">🍽️ Harmonizar com refeições</label></div>
          <div class="chip"><input type="checkbox" name="motivo[]" id="mt4" value="Experiência gastronômica"><label for="mt4">🌟 Experiência gastronômica</label></div>
          <div class="chip"><input type="checkbox" name="motivo[]" id="mt5" value="Status / sofisticação"><label for="mt5">👑 Status / sofisticação</label></div>
        </div>
      </div>
      <div class="field">
        <label>O que faz escolher um produto específico?</label>
        <textarea name="escolha" placeholder="Ex: rótulo bonito, indicação de amigo, uva preferida, país de origem..."></textarea>
      </div>
    </div>
  </div>

  <!-- 5. Dores -->
  <div class="card">
    <div class="card-header">
      <span class="card-num">05</span>
      <span class="card-icon">😣</span>
      <div class="card-info">
        <h2>Dores e dificuldades</h2>
        <p>Barreiras na jornada de compra</p>
      </div>
    </div>
    <div class="card-body">
      <div class="field">
        <label>O que mais dificulta na hora de escolher um vinho ou produto gourmet?</label>
        <textarea name="dor" placeholder="Ex: não sabe qual rótulo escolher, preços altos, falta de informação na embalagem, sem ajuda especializada..."></textarea>
      </div>
      <div class="field">
        <label>Nome da persona (opcional)</label>
        <input type="text" name="nome" placeholder="Ex: Pedro, 38 anos — Apreciador casual">
      </div>
    </div>
  </div>

  <!-- SUBMIT -->
  <div class="submit-card">
    <button type="submit" class="btn-submit">
      Salvar Persona &nbsp;→
    </button>
    <p class="submit-note">Os dados são salvos no banco de dados PostgreSQL</p>
    <p class="powered">Desenvolvido por <span>ApceX</span> · Tecnologia e estratégia</p>
  </div>

</form>
</div>

<!-- MODAL -->
<div class="overlay" id="overlay">
  <div class="modal">
    <div class="modal-icon">🚀</div>
    <h3>Persona Registrada!</h3>
    <p>As informações foram salvas com sucesso no banco de dados. Você pode registrar uma nova persona a seguir.</p>
    <button class="btn-close" onclick="closeModal()">Registrar outra</button>
  </div>
</div>

<script>
  function updateTicket(v) {
    v = parseInt(v);
    document.getElementById('ticketVal').textContent = v >= 1000 ? 'R$ 1.000+' : 'R$ ' + v;
    updateProgress();
  }

  const sectionChecks = [
    () => !!document.querySelector('input[name="genero"]:checked') || !!document.querySelector('input[name="idade"]:checked'),
    () => !!document.querySelector('input[name="fv"]:checked'),
    () => !!document.querySelector('input[name="atrai"]:checked'),
    () => !!document.querySelector('input[name="motivo"]:checked'),
    () => document.querySelector('textarea[name="dor"]')?.value?.trim().length > 0
  ];

  function updateProgress() {
    const filled = sectionChecks.filter(fn => fn()).length;
    document.getElementById('progFill').style.width = (filled / 5 * 100) + '%';
    document.getElementById('progLabel').textContent = filled + ' / 5';
    document.querySelectorAll('.prog-dot').forEach((dot, i) => {
      dot.classList.toggle('active', i < filled);
    });
  }

  document.querySelectorAll('input, textarea').forEach(el => {
    el.addEventListener('change', updateProgress);
    el.addEventListener('input', updateProgress);
  });

  function handleSubmit(e) {
    e.preventDefault();
    // Submit the form normally since it's now POST
    e.target.submit();
  }

  function closeModal() {
    document.getElementById('overlay').classList.remove('show');
    document.getElementById('mainForm').reset();
    document.getElementById('ticketVal').textContent = 'R$ 150';
    document.getElementById('ticketSlider').value = 150;
    updateProgress();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // Show modal if message exists
  <?php if ($message): ?>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('overlay').classList.add('show');
  });
  <?php endif; ?>
</script>
</body>
</html>