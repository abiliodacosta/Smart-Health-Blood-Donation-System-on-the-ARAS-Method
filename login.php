<?php
require_once 'src/Config.php';
require_once 'src/Auth.php';

Auth::init();
if (isset($_SESSION['user_id'])) {
    header("Location: index");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $login_result = Auth::login($pdo, $username, $password);
    if ($login_result === true) {
        header("Location: index");
        exit();
    } elseif ($login_result === "locked") {
        $error = 'Kontu ne\'e xave hela. Kontaktu administrator!';
    } else {
        $error = 'Username ka Password sala!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Smart-Health DSS</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        .btn-primary {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }

        .text-primary {
            color: #4f46e5 !important;
        }

        @media (max-width: 576px) {
            .container {
                padding: 1rem !important;
            }

            .card {
                border-radius: 1rem !important;
                min-height: auto !important;
            }

            .p-5 {
                padding: 2rem !important;
            }
        }
    </style>
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-10">
                <div class="card o-hidden border-0 shadow-lg my-5" style="max-width: 800px; margin-left: auto; margin-right: auto;">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-flex bg-white align-items-center justify-content-center text-center p-4 p-lg-5" style="border-right: 1px solid #e3e6f0;">
                                <div class="w-100 py-4">
                                    <h5 class="font-weight-bold mb-4" style="color: #000;">Pagina Login Smart-Health</h5>
                                    <div class="mb-4">
                                        <img src="assets/images/cvtl_logo.png" alt="CVTL Logo" class="img-fluid" style="max-width: 100px;">
                                    </div>
                                    <p class="text-gray-700 px-4 mb-4" style="text-align: center; font-size: 0.95rem; font-weight: 500; line-height: 1.4;">Inovasaun Smart-Health Doasaun Raan iha Cruz Vermelha de Timor-Leste bazeia ba Métode ARAS</p>
                                    <div>
                                        <div class="badge badge-danger p-2 px-4 shadow-sm" style="font-size: 0.85rem; border-radius: 30px;">
                                            <i class="fas fa-hand-holding-heart mr-2"></i> Saving Lives Together
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5 p-lg-5">
                                    <div class="text-center d-none d-lg-block">
                                        <img src="assets/images/favicon.png" alt="Logo" class="img-fluid" style="max-width: 120px; margin-bottom: 1rem;">
                                        <h1 class="h4 text-gray-900 mb-4">Smart-Health</h1>
                                    </div>

                                    <?php if ($error): ?>
                                        <div class="alert alert-danger small py-2"><?php echo $error; ?></div>
                                    <?php endif; ?>

                                    <form class="user" method="POST">
                                        <div class="form-group">
                                            <input type="text" name="username" class="form-control form-control-user" placeholder="Enter Username..." required>
                                        </div>
                                        <div class="form-group position-relative">
                                            <input type="password" name="password" id="password" class="form-control form-control-user" placeholder="Password" required>
                                            <i class="fa-solid fa-eye position-absolute" id="togglePassword" style="right: 20px; top: 15px; cursor: pointer; color: #ccc;"></i>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <span class="small text-muted">Cruz Vermelha de Timor-Leste</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', function(e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>

</html>