<?php
include 'connection.php';

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
WHERE [Cedula cliente] = ?
";

$params = array($item_id);
$stmt = sqlsrv_prepare($conn, $sql, $params);

if ($stmt === false) {
    die('Error en la ejecución de la consulta');
}

if (sqlsrv_execute($stmt) === false) {
    die(print_r(sqlsrv_errors(), true));
}

$results = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $row['FechaRegistroCaso'] = formatDate($row['FechaRegistroCaso']);
    $results[] = $row;
}

function formatDate($date) {
    if ($date instanceof DateTime) {
        return $date->format('Y-m-d H:i:s');
    } elseif (is_object($date)) {
        return $date->format('Y-m-d H:i:s');
    }
    return $date;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo json_encode($results);
?>
