<?php
include 'koneksi.php';
include 'kmp.php';

$hasil_pencarian = [];
$keyword = "";
$mode_pencarian = "bugis_indo";

if (isset($_POST['cari'])) {
    $keyword = $_POST['keyword'];
    $mode_pencarian = $_POST['mode_pencarian'];
    
    $query = mysqli_query($conn, "SELECT * FROM kamus");
    
    while ($row = mysqli_fetch_assoc($query)) {

        if ($mode_pencarian == "bugis_indo") {
            $teks_database = $row['kata_bugis'];
        } else {
            $teks_database = $row['arti_indonesia'];
        }

        $text_lower = strtolower($teks_database);
        $pattern_lower = strtolower($keyword);

        if (kmpSearchIndex($text_lower, $pattern_lower) != -1) {
            $hasil_pencarian[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamus Cantik Bugis</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        
        body {
            font-family: 'Poppins', sans-serif;

            background: linear-gradient(135deg, #E3D5CA 0%, #D6CCC2 100%); 
            min-height: 100vh;
            padding-bottom: 50px;
            color: #5D4037;
        }

        .main-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(141, 110, 99, 0.2);
            overflow: hidden;
            background-color: rgba(255, 255, 255, 0.95);
        }

        .card-header-brown {
            background: linear-gradient(to right, #8D6E63, #A1887F);
            color: white;
            padding: 25px;
            border-bottom: none;
            text-align: center;
        }

        .form-select, .form-control {
            border-radius: 50px;
            border: 2px solid #D7CCC8;
            padding: 12px 20px;
            color: #5D4037;
        }

        .form-select:focus, .form-control:focus {
            border-color: #8D6E63;
            box-shadow: 0 0 0 0.2rem rgba(141, 110, 99, 0.25);
        }

        .btn-brown {
            background: #8D6E63;
            color: white;
            border-radius: 50px;
            padding: 12px 30px;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-brown:hover {
            background: #6D4C41;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(109, 76, 65, 0.4);
        }

        .table-custom thead {
            background-color: #8D6E63;
            color: white;
        }
        
        .table-custom th {
            border: none;
            padding: 15px;
        }

        .table-custom td {
            padding: 15px;
            vertical-align: middle;
            color: #4E342E;
        }

        .table-hover tbody tr:hover {
            background-color: #EFEBE9;
        }

        .fade-in {
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .empty-state {
            color: #8D6E63;
            opacity: 0.7;
        }

        hr {
            border-color: #BCAAA4;
            opacity: 0.5;
        }

        /* Icon Input Group */
        .input-group-text {
            color: #8D6E63 !important;
            border-color: #D7CCC8 !important;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card main-card fade-in">
                <div class="card-header-brown">
                    <h3><i class="fas fa-book-open me-2"></i> Kamus Bahasa Bugis</h3>
                    <p class="mb-0 small">Metode String Matching: Knuth-Morris-Pratt (KMP)</p>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="mode_pencarian" class="form-select">
                                    <option value="bugis_indo" <?php if($mode_pencarian == 'bugis_indo') echo 'selected'; ?>>
                                        Bugis ➞ Indonesia
                                    </option>
                                    <option value="indo_bugis" <?php if($mode_pencarian == 'indo_bugis') echo 'selected'; ?>>
                                        Indonesia ➞ Bugis
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-0 ps-3" style="border-radius: 50px 0 0 50px; border: 2px solid #D7CCC8 !important; border-right: none !important;">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="keyword" class="form-control" style="border-left: none; border-radius: 0 50px 50px 0;" placeholder="Ketik kata disini..." value="<?php echo htmlspecialchars($keyword); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-brown w-100" type="submit" name="cari">Cari</button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    <?php if(isset($_POST['cari'])): ?>
                        
                        <?php if(count($hasil_pencarian) > 0): ?>
                            <div class="alert alert-light border-0 shadow-sm text-center" style="color: #6D4C41; background-color: #EFEBE9;">
                                <i class="fas fa-check-circle"></i> Ditemukan <b><?php echo count($hasil_pencarian); ?></b> kata yang cocok.
                            </div>
                            
                            <div class="table-responsive rounded-3 overflow-hidden shadow-sm">
                                <table class="table table-custom table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <?php if ($mode_pencarian == "bugis_indo"): ?>
                                                <th><i class="fas fa-language"></i> Bahasa Bugis</th>
                                                <th><i class="fas fa-flag"></i> Arti Indonesia</th>
                                            <?php else: ?>
                                                <th><i class="fas fa-flag"></i> Bahasa Indonesia</th>
                                                <th><i class="fas fa-language"></i> Arti Bugis</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($hasil_pencarian as $data): ?>
                                        <tr>
                                            <?php if ($mode_pencarian == "bugis_indo"): ?>
                                                <td class="fw-bold" style="color: #3E2723;"><?php echo $data['kata_bugis']; ?></td>
                                                <td><?php echo $data['arti_indonesia']; ?></td>
                                            <?php else: ?>
                                                <td class="fw-bold" style="color: #3E2723;"><?php echo $data['arti_indonesia']; ?></td>
                                                <td><?php echo $data['kata_bugis']; ?></td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 fade-in">
                                <i class="fas fa-box-open fa-3x empty-state mb-3"></i>
                                <p class="text-muted">Maaf, kata <b>"<?php echo htmlspecialchars($keyword); ?>"</b> tidak ditemukan.</p>
                            </div>
                        <?php endif; ?>
                    
                    <?php else: ?>
                        <div class="text-center py-5 fade-in">
                            <i class="fas fa-spell-check fa-4x mb-3" style="color: #D7CCC8;"></i>
                            <h5 style="color: #8D6E63;">Mari Belajar Bahasa Bugis!</h5>
                            <p class="text-muted small">Masukkan kata pada kolom pencarian di atas.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
            
            <div class="text-center mt-4 text-secondary small">
                &copy; <?php echo date('Y'); ?> Kamus Digital | Made with <i class="fas fa-heart text-danger"></i>
            </div>

        </div>
    </div>
</div>

</body>
</html>