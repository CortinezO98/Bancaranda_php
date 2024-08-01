<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Bancaranda'; ?></title>
    <link rel="icon" href="img/Logoseacrh.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <?php if (isset($style)): ?>
        <?php echo $style ?>
    <?php endif; ?>
</head>
<body style="background-color: white">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="img/Logoseacrh.png" alt="logo" height="25" class="d-inline-block align-text-top">
                Bancolombia Aranda
            </a>
            <span class="navbar-text ms-auto">
                <a class="btn btn-primary" href="https://centrodecontacto.iq-online.net.co/tipificadores/inbound/soporteComercial/formularioc2c/clickToCall/" target="_blank">
                    <i class="fa-solid fa-phone"></i> Llámanos 
                </a>
            </span>
        </div>
    </nav>

    <div class="container pt-2">    
        <?php echo $content; ?>
    </div> 

    <br><br>
       
    <footer class="footer bg-dark text-white mt-auto py-3">
        <div class="container text-center">
            <span>Copyright 2024 IQ Outsourcing</span>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/f3f4e0e04a.js" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>

    
    <?php if (isset($script)): ?>
        <?php echo $script; ?>
    <?php endif; ?>
    
    <script src="https://centrodecontacto.iq-online.net.co/fop2/admin/plugins/phonepro/webwidget/chat-widget.js"></script><script>fop2chatbroker.Setup({"page":"chat","lang":"es","bubble_color":"#0D6EFD","text_color":"#FFFFFF","autocreate_user":false});</script>
    
</body>
</html>
