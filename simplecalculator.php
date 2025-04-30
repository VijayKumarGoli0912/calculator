<?php
session_start();
if (!isset($_SESSION['history'])) $_SESSION['history'] = [];

$display = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['input'] ?? '';
    $num = $_POST['num'] ?? '';
    $op = $_POST['op'] ?? '';
    $equal = $_POST['equal'] ?? '';
    $extra = $_POST['extra'] ?? '';
    $clear = $_POST['clear'] ?? '';
    $allclear = $_POST['allclear'] ?? '';

    if ($allclear) {
        $display = '';
        $_SESSION['history'] = [];
    } elseif ($clear) {
        $display = substr($input, 0, -1);
    } elseif ($num) {
        $display = $input . $num;
    } elseif ($op) {
        $display = $input . $op;
    } elseif ($extra) {
        try {
            $value = floatval($input);
            if ($extra === '√') {
                $display = sqrt($value);
            } elseif ($extra === 'x²') {
                $display = pow($value, 2);
            } elseif ($extra === '%') {
                $display = $value / 100;
            } elseif ($extra === 'log') {
                $display = $value > 0 ? log10($value) : "Error";
            }
            $_SESSION['history'][] = "$input $extra = $display";
            $_SESSION['history'] = array_slice($_SESSION['history'], -5);
        } catch (Throwable $e) {
            $display = "Error";
        }
    } elseif ($equal) {
        $expression = trim($input);
        if (preg_match('/^[0-9+\-*\/.()% ]+$/', $expression)) {
            try {
                $result = eval("return $expression;");
                if ($result !== false) {
                    $display = $result;
                    $_SESSION['history'][] = "$expression = $result";
                    $_SESSION['history'] = array_slice($_SESSION['history'], -5);
                } else {
                    $display = "Error";
                }
            } catch (Throwable $e) {
                $display = "Error";
            }
        } else {
            $display = "Invalid";
        }
    }
}
?>
<?php include "index.html"; ?>