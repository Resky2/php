<?php
session_start();

$hasil = null;
$error = null;
$angka1 = '';
$angka2 = '';
$operator = '+';

if (!isset($_SESSION['riwayat'])) {
    $_SESSION['riwayat'] = [];
}

// Hapus riwayat
if (isset($_POST['hapus_riwayat'])) {
    $_SESSION['riwayat'] = [];
}

// Proses perhitungan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hitung'])) {
    $angka1   = trim($_POST['angka1'] ?? '');
    $angka2   = trim($_POST['angka2'] ?? '');
    $operator = $_POST['operator'] ?? '+';

    $operatorValid = ['+', '-', '*', '/', '%', '^'];

    if (!is_numeric($angka1) || !is_numeric($angka2)) {
        $error = 'Masukkan angka yang valid.';
    } elseif (!in_array($operator, $operatorValid, true)) {
        $error = 'Operator tidak valid.';
    } else {
        $a = (float) $angka1;
        $b = (float) $angka2;

        switch ($operator) {
            case '+':
                $hasil = $a + $b;
                break;
            case '-':
                $hasil = $a - $b;
                break;
            case '*':
                $hasil = $a * $b;
                break;
            case '/':
                if ($b == 0) {
                    $error = 'Tidak bisa membagi dengan nol.';
                } else {
                    $hasil = $a / $b;
                }
                break;
            case '%':
                if ($b == 0) {
                    $error = 'Tidak bisa modulus dengan nol.';
                } else {
                    $hasil = fmod($a, $b);
                }
                break;
            case '^':
                $hasil = pow($a, $b);
                break;
        }

        if ($hasil !== null && $error === null) {
            $hasil = round($hasil, 10);
            $simbol = $operator === '*' ? '×' : ($operator === '/' ? '÷' : $operator);
            array_unshift($_SESSION['riwayat'], "$a $simbol $b = $hasil");
            $_SESSION['riwayat'] = array_slice($_SESSION['riwayat'], 0, 10);
        }
    }
}

function e($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator PHP</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .kalkulator {
            background: #fff;
            width: 100%;
            max-width: 380px;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,.3);
        }
        h1 { margin: 0 0 16px; font-size: 22px; text-align: center; color: #1e3c72; }
        label { display: block; font-size: 13px; color: #555; margin-bottom: 4px; }
        input[type="number"], select {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 14px;
        }
        input:focus, select:focus { outline: 2px solid #2a5298; border-color: transparent; }
        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: #2a5298;
            color: #fff;
        }
        button:hover { background: #1e3c72; }
        .hasil, .error {
            margin-top: 16px;
            padding: 14px;
            border-radius: 8px;
            text-align: center;
            font-size: 20px;
            word-break: break-all;
        }
        .hasil { background: #e8f5e9; color: #2e7d32; }
        .error { background: #ffebee; color: #c62828; font-size: 15px; }
        .riwayat { margin-top: 20px; }
        .riwayat h2 { font-size: 15px; margin: 0 0 8px; color: #333; }
        .riwayat ul { list-style: none; padding: 0; margin: 0 0 10px; font-size: 14px; color: #444; }
        .riwayat li { padding: 6px 0; border-bottom: 1px solid #eee; }
        .btn-hapus { background: #c62828; padding: 8px; font-size: 13px; }
        .btn-hapus:hover { background: #8e0000; }
    </style>
</head>
<body>
    <div class="kalkulator">
        <h1>Kalkulator PHP</h1>

        <form method="post" action="">
            <label for="angka1">Angka pertama</label>
            <input type="number" step="any" id="angka1" name="angka1" value="<?= e($angka1) ?>" required>

            <label for="operator">Operator</label>
            <select id="operator" name="operator">
                <option value="+" <?= $operator === '+' ? 'selected' : '' ?>>+ Tambah</option>
                <option value="-" <?= $operator === '-' ? 'selected' : '' ?>>- Kurang</option>
                <option value="*" <?= $operator === '*' ? 'selected' : '' ?>>× Kali</option>
                <option value="/" <?= $operator === '/' ? 'selected' : '' ?>>÷ Bagi</option>
                <option value="%" <?= $operator === '%' ? 'selected' : '' ?>>% Modulus</option>
                <option value="^" <?= $operator === '^' ? 'selected' : '' ?>>^ Pangkat</option>
            </select>

            <label for="angka2">Angka kedua</label>
            <input type="number" step="any" id="angka2" name="angka2" value="<?= e($angka2) ?>" required>

            <button type="submit" name="hitung">Hitung</button>
        </form>

        <?php if ($error !== null): ?>
            <div class="error"><?= e($error) ?></div>
        <?php elseif ($hasil !== null): ?>
            <div class="hasil">Hasil: <strong><?= e($hasil) ?></strong></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['riwayat'])): ?>
            <div class="riwayat">
                <h2>Riwayat</h2>
                <ul>
                    <?php foreach ($_SESSION['riwayat'] as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
                <form method="post" action="">
                    <button type="submit" name="hapus_riwayat" class="btn-hapus">Hapus riwayat</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
