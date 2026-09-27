<?php
session_start();

// Jika sudah login, langsung ke laporan
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

// Proses login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // Login sementara
    if ($username === "admin" && $password === "admin123") {

        $_SESSION["login"] = true;
        $_SESSION["username"] = $username;

        header("Location: dashboard.php");
        exit;

    } else {

        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Dashboard</title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            min-height: 100vh;

            display: flex;

            justify-content: center;
            align-items: center;

            padding: 20px;

            background:
                radial-gradient(
                    circle at top left,
                    #dbeafe,
                    transparent 35%
                ),
                radial-gradient(
                    circle at bottom right,
                    #bfdbfe,
                    transparent 35%
                ),
                #eef4ff;
        }


        /* ==========================
           CONTAINER UTAMA
        ========================== */

        .login-container {

            width: 100%;

            max-width: 1000px;

            min-height: 580px;

            display: flex;

            background: white;

            border-radius: 25px;

            overflow: hidden;

            box-shadow:
                0 25px 60px
                rgba(0, 0, 0, 0.15);
        }


        /* ==========================
           PANEL KIRI
        ========================== */

        .left-panel {

            width: 45%;

            padding: 55px;

            color: white;

            display: flex;

            flex-direction: column;

            justify-content: center;

            background:
                linear-gradient(
                    145deg,
                    #0645a5,
                    #0878df
                );

            position: relative;

            overflow: hidden;
        }


        /* Lingkaran dekorasi */

        .left-panel::before {

            content: "";

            position: absolute;

            width: 350px;
            height: 350px;

            background:
                rgba(255,255,255,0.08);

            border-radius: 50%;

            top: -150px;
            right: -120px;
        }


        .left-panel::after {

            content: "";

            position: absolute;

            width: 300px;
            height: 300px;

            background:
                rgba(255,255,255,0.06);

            border-radius: 50%;

            bottom: -150px;
            left: -100px;
        }


        /* ==========================
           LOGO PRIMA
        ========================== */

        .logo {

            width: 280px;

            height: 100px;

            display: flex;

            align-items: center;

            justify-content: flex-start;

            margin-bottom: 35px;

            position: relative;

            z-index: 2;
        }


        .logo img {

            width: 100%;

            height: 100%;

            object-fit: contain;

            object-position: left center;

            display: block;
        }


        /* ==========================
           JUDUL
        ========================== */

        .left-panel h1 {

            font-size: 38px;

            line-height: 1.2;

            margin-bottom: 20px;

            position: relative;

z-index: 2;
        }


        .company-name {

            font-size: 19px;

            font-weight: bold;

            margin-bottom: 15px;

            color: #ffffff;

            position: relative;

            z-index: 2;
        }


        .left-panel p {

            font-size: 16px;

            line-height: 1.7;

            color: #e8f2ff;

            position: relative;

            z-index: 2;
        }


        /* ==========================
           INFO BOX
        ========================== */

        .info-box {

            margin-top: 30px;

            padding: 17px 20px;

            border-radius: 15px;

            background:
                rgba(255,255,255,0.12);

            font-size: 14px;

            line-height: 1.5;

            position: relative;

            z-index: 2;

            border:
                1px solid
                rgba(255,255,255,0.12);
        }


        /* ==========================
           PANEL KANAN
        ========================== */

        .right-panel {

            width: 55%;

            padding: 60px;

            display: flex;

            flex-direction: column;

            justify-content: center;
        }


        /* ==========================
           ICON LOGIN
        ========================== */

        .login-icon {

            width: 70px;

            height: 70px;

            margin:
                0 auto 20px;

            border-radius: 50%;

            background: #e8f1ff;

            color: #1976d2;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 32px;
        }


        .right-panel h2 {

            text-align: center;

            font-size: 32px;

            color: #173b67;

            margin-bottom: 10px;
        }


        .subtitle {

            text-align: center;

            color: #718096;

            margin-bottom: 35px;

            line-height: 1.6;
        }


        /* ==========================
           ERROR
        ========================== */

        .error {

            background: #fff0f0;

            color: #d93025;

            border:
                1px solid
                #ffcaca;

            padding: 12px;

            border-radius: 10px;

            margin-bottom: 20px;

            text-align: center;

            font-size: 14px;
        }


        /* ==========================
           FORM
        ========================== */

        .form-group {

            margin-bottom: 20px;
        }


        .form-group label {

            display: block;

            font-size: 14px;

            font-weight: bold;

            color: #34495e;

            margin-bottom: 8px;
        }


        .input-wrapper {

            position: relative;

            width: 100%;
        }


        .input-icon {

            position: absolute;

            left: 17px;

            top: 50%;

            transform:
                translateY(-50%);

            font-size: 19px;

            color: #1976d2;

            pointer-events: none;

            z-index: 2;
        }


        .input-wrapper input {

            width: 100%;

            height: 54px;

            padding:
                15px
                50px
                15px
                50px;

            border:
                1px solid
                #d6e0ec;

            border-radius: 12px;

            font-size: 15px;

            outline: none;

            transition: 0.3s;

            background: #f9fbfe;
        }


        .input-wrapper input:focus {

            border-color: #1976d2;

            background: white;

            box-shadow:
                0 0 0 4px
                rgba(25,118,210,0.10);
        }


        .input-wrapper input::placeholder {

            color: #9aa8b8;
        }


        /* ==========================
           TOMBOL MATA PASSWORD
        ========================== */

        .password-toggle {

            position: absolute;

            right: 12px;

top: 50%;

            transform:
                translateY(-50%);

            width: 40px;

            height: 40px;

            border: none;

            background: transparent;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 18px;

            z-index: 5;
        }


        .password-toggle:hover {

            background: #eef5ff;

            border-radius: 8px;
        }


        /* ==========================
           TOMBOL LOGIN
        ========================== */

        .login-button {

            width: 100%;

            height: 54px;

            border: none;

            border-radius: 12px;

            margin-top: 5px;

            background:
                linear-gradient(
                    135deg,
                    #1976d2,
                    #075bbb
                );

            color: white;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            transition: 0.3s;

            box-shadow:
                0 8px 18px
                rgba(25,118,210,0.25);
        }


        .login-button:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(25,118,210,0.35);
        }


        .login-button:active {

            transform:
                translateY(0);
        }


        /* ==========================
           FOOTER
        ========================== */

        .footer {

            text-align: center;

            margin-top: 30px;

            padding-top: 20px;

            border-top:
                1px solid
                #edf1f5;

            color: #a0aec0;

            font-size: 12px;
        }


        /* ==========================
           RESPONSIVE TABLET / HP
        ========================== */

        @media (max-width: 750px) {

            body {

                padding: 15px;

            }


            .login-container {

                flex-direction: column;

                min-height: auto;

                border-radius: 20px;
            }


            .left-panel {

                width: 100%;

                padding: 35px 25px;

                text-align: center;

                min-height: 350px;

                align-items: center;
            }


            .logo {

                width: 230px;

                height: 85px;

                margin-left: auto;

                margin-right: auto;

                margin-bottom: 25px;
            }


            .left-panel h1 {

                font-size: 30px;

            }


            .company-name {

                font-size: 17px;

            }


            .left-panel p {

                font-size: 14px;

            }


            .info-box {

                width: 100%;

                margin-top: 20px;

            }


            .right-panel {

                width: 100%;

                padding: 35px 25px;
            }


            .right-panel h2 {

                font-size: 28px;
            }

        }


        /* ==========================
           HP KECIL
        ========================== */

        @media (max-width: 400px) {

            .left-panel {

                padding: 30px 20px;

            }


            .logo {

                width: 200px;

                height: 75px;
            }


            .left-panel h1 {

                font-size: 26px;

            }


            .right-panel {

                padding: 30px 20px;

            }

        }

    </style>

</head>


<body>


<div class="login-container">


    <!-- ==================================
         PANEL KIRI
    =================================== -->

    <div class="left-panel">


        <!-- LOGO PERUSAHAAN -->

        <div class="logo">

            <img
                src="logo.jpg"
                alt="Logo PRIMA"
            >

        </div>

<h1>
            Laporan<br>
            Stok Barang
        </h1>


        <div class="company-name">
            PT Prima Sarana Gemilang
        </div>


        <p>
            Sistem informasi untuk memantau
            data stok barang dengan mudah,
            cepat dan akurat.
        </p>


        <div class="info-box">

            📊 &nbsp;
            Kelola data barang dan lihat
            laporan stok secara praktis.

        </div>


    </div>


    <!-- ==================================
         PANEL KANAN
    =================================== -->

    <div class="right-panel">


        <div class="login-icon">
            👤
        </div>


        <h2>
            Login
        </h2>


        <p class="subtitle">

            Silakan masukkan username dan
            password untuk melanjutkan.

        </p>


        <?php if ($error != ""): ?>

            <div class="error">

                ⚠️
                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">


            <!-- USERNAME -->

            <div class="form-group">

                <label for="username">
                    Username
                </label>


                <div class="input-wrapper">

                    <span class="input-icon">
                        👤
                    </span>


                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required
                    >

                </div>

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>


                <div class="input-wrapper">

                    <span class="input-icon">
                        🔒
                    </span>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >


                    <button
                        type="button"
                        class="password-toggle"
                        onclick="togglePassword()"
                        id="eye"
                        aria-label="Tampilkan password"
                    >
                        👁️
                    </button>

                </div>

            </div>


            <!-- TOMBOL LOGIN -->

            <button
                type="submit"
                class="login-button"
            >

                Masuk &nbsp; →

            </button>


        </form>


        <div class="footer">

            © <?= date("Y") ?>
            PT Prima Sarana Gemilang

        </div>


    </div>

</div>



<script>

function togglePassword() {

    const password =
        document.getElementById("password");

    const eye =
        document.getElementById("eye");


    if (password.type === "password") {

        password.type = "text";

        eye.textContent = "🙈";

        eye.setAttribute(
            "aria-label",
            "Sembunyikan password"
        );

    } else {

        password.type = "password";

        eye.textContent = "👁️";

        eye.setAttribute(
            "aria-label",
            "Tampilkan password"
        );

    }

}

</script>


</body>

</html>