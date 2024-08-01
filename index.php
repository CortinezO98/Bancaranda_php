<?php
$title = 'Bancaranda';
$content = '
<div class="row justify-content-center">
    <div class="col-lg-9 col-md-11 col-sm-12">
        <div class="logo-container text-center my-3">
            <img src="img/Imagen1.jpg" class="logo img-fluid" alt="logo">
        </div>
        <div class="text-center my-4">
            <p><strong>
                Bienvenido a la mesa de atencion comercial Bancolombia, aqui puedes consultar estado de solicitudes comerciales, y de ser necesario ponerte en contacto con el equipo de soporte Comercial.
            </strong></p>
        </div>
        <div class="card mt-4">
            <div class="card-header text-center">
                Consulta cliente
            </div>
            <div class="card-body">
                <form method="GET" id="searchForm">
                    <div class="input-group my-3">
                        <input type="number" class="form-control" name="item_id" placeholder="Ingresa el numero de cedula" value="' . (isset($_GET['item_id']) ? htmlspecialchars($_GET['item_id']) : '') . '">
                        <button class="btn btn-primary btn-lg" type="submit" id="btnSearch">Buscar</button>
                    </div>
                </form>
                <div class="results-container"></div>
            </div>
        </div>
    </div>
</div>';

include('templates/base.php');
?>
