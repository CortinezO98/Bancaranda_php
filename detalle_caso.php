<?php
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
include('templates/base.php');
include('includes/connection.php');

$item_id = isset($_GET['item_id']) ? filter_var($_GET['item_id'], FILTER_SANITIZE_NUMBER_INT) : '';
if (!is_numeric($item_id)) {
    die('ID del ítem no es válido');
}
$sql = "
WITH SourceData AS (
    SELECT 
        ProjectName as Proyecto,
        ComposedId AS Caso,
        RegistrationDate AS FechaRegistro,
        GroupName AS GrupoSoporte,
        SpecialistName AS Especialista,
        ServiceName AS Servicio,
        CategoryName AS Categoria,
        CategoryHierarchy as Jerarquia,
        StateName AS EstadoCaso,
        RegistrationDate AS FechaRegistroCaso,
        AttentionDateExpected AS FechaAtencionEstimada,
        AttentionDate AS FechaAtencion,
        SolutionDateReal AS FechaSolucionReal,
        SolutionDateExpected AS FechaSolucionEstimada,
        ClosedDate AS FechaCierre,
        CustomerName AS NombreCliente,
        CASE 
            WHEN StateName = 'Cerrado' THEN 'Cerrado'
            WHEN StateName LIKE 'Solucionad%' THEN 'Cerrado'
            WHEN StateName LIKE 'Registrado%' THEN 'Abierto'
            WHEN StateName LIKE 'En Proceso%' THEN 'Abierto'
            ELSE 'Otros'
        END AS EstadoAbiertoCerrado,
        Subject AS Asunto,
        ItemId,
        AuthorName AS CreadorCaso,
        conf.FL_STR_NAME as CampoAdicional,
        fd.FL_STR_FIELD_VALUE as ValorCampoAdicional
    FROM dbo.V_ASDK_CASE_DETAILS c WITH (NOLOCK)
    INNER JOIN AFW_ADD_FIELDS_DATA FD ON FD.FL_INT_ID_CASO = c.ItemId
    INNER JOIN dbo.AFW_ADD_FIELDS_CONFIG CONF ON FD.FL_INT_ID_FIELD = CONF.FL_INT_ID_FIELD
    WHERE ServiceName = 'Bancolombia - Soporte Comercial'
),
PivotData AS (
    SELECT 
        Proyecto,
        Caso,
        FechaRegistro,
        GrupoSoporte,
        Especialista,
        Servicio,
        Categoria,
        Jerarquia,
        EstadoCaso,
        FechaRegistroCaso,
        FechaAtencionEstimada,
        FechaAtencion,
        FechaSolucionReal,
        FechaSolucionEstimada,
        FechaCierre,
        NombreCliente,
        EstadoAbiertoCerrado,
        Asunto,
        ItemId,
        CreadorCaso,
        [Canal de ingreso], 
        [Region de radicacion],
        [Radicado SIB], 
        [Cedula cliente],  
        [Codigo vendedor],
        [Etapa],
        [Centro],
        [Guiainmobiliario],
        [Correocomercial]
    FROM 
        SourceData
    PIVOT (
        MAX(ValorCampoAdicional)
        FOR CampoAdicional IN ([Canal de ingreso], [Region de radicacion], [Radicado SIB], [Cedula cliente], [Codigo vendedor], [Etapa], [Centro], [Guiainmobiliario], [Correocomercial])
    ) AS PivotTable
)
SELECT *
FROM PivotData
WHERE ItemId = ?
";

$params = array($item_id);
$stmt = sqlsrv_prepare($conn, $sql, $params);

if ($stmt === false) {
    die('Error en la ejecución de la consulta');
}

if (sqlsrv_execute($stmt) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$detalle_caso = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

if (!$detalle_caso) {
    die('No se encontraron detalles para el item_id proporcionado');
}

function formatDate($date) {
    if ($date instanceof DateTime) {
        return $date->format('d-m-Y H:i:s');
    }
    return $date;
}
?>

<div class="container pt-2">
    <div class="row justify-content-center">
        <div class="col offset-lg-1 col-lg-10">
            <div class="logo-container my-3">
                <img src="img/Imagen1.jpg" class="logo img-fluid" alt="logo">
            </div>
            <div class="card mt-4">
                <div class="card-header text-center">
                    <p><strong>Detalles del Caso</strong></p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th scope="row">Proyecto</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Proyecto']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Caso</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Caso']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha Registro</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaRegistro'])); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Servicio</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Servicio']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Categoría</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Categoria']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Jerarquía</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Jerarquia']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Estado del Caso</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['EstadoCaso']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha Registro del Caso</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaRegistroCaso'])); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha Atención Estimada</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaAtencionEstimada'])); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha Atención</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaAtencion'])); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha Solución Real</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaSolucionReal'])); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha Solución Estimada</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaSolucionEstimada'])); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Fecha de Cierre</th>
                                    <td><?php echo htmlspecialchars(formatDate($detalle_caso['FechaCierre'])); ?></td>
                                </tr>
                                
                                <tr>
                                    <th scope="row">Estado Abierto/Cerrado</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['EstadoAbiertoCerrado']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Asunto</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Asunto']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Creador del Caso</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['CreadorCaso']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Canal de Ingreso</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Canal de ingreso']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Region de radicacion</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Region de radicacion']); ?></td>
                                </tr>
			                    <tr>
                                    <th scope="row">Radicado SIB</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Radicado SIB']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Cedula cliente</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Cedula cliente']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Codigo vendedor</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Codigo vendedor']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Etapa</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Etapa']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Centro</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Centro']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Guia inmobiliario</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Guiainmobiliario']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Correo comercial</th>
                                    <td><?php echo htmlspecialchars($detalle_caso['Correocomercial']); ?></td>
                                </tr>

                                	
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center py-2">
                        <a href="index.php" class="btn btn-primary">Volver</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>
<br><br>

<?php include('templates/base/footer.php'); ?>