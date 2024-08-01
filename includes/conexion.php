<?php
$serverName = "172.26.18.9";
$databaseName = "IQ_ASDKV8";
$myusername = "PortalBancolombia";
$mypassword = '6$KLpUg@3061v&';

// Conexión al servidor de base de datos
$connectionInfo = array("Database" => $databaseName, "UID" => $myusername, "PWD" => $mypassword);
$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Consulta SQL con PIVOT
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
        [Canal de ingreso], -- Reemplaza con los nombres reales de los campos adicionales
        [Region de radicacion],
        [Radicado SIB], -- Reemplaza con los nombres reales de los campos adicionales
        [Cedula cliente],  -- Reemplaza con los nombres reales de los campos adicionales
        [Codigo vendedor],
        [Etapa],
        [Centro],
        [Guiainmobiliario],
        [Correocomercial]
    FROM 
        SourceData
    PIVOT (
        MAX(ValorCampoAdicional)
        FOR CampoAdicional IN ([Canal de ingreso], [Region de radicacion], [Radicado SIB], [Cedula cliente], [Codigo vendedor], [Etapa], [Centro], [Guiainmobiliario], [Correocomercial]) -- Reemplaza con los nombres reales de los campos adicionales
    ) AS PivotTable
)
SELECT *
FROM PivotData
WHERE [Cedula cliente] = '123456789'          
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Iterar sobre los resultados y mostrar los datos
echo "<table border='1'>";
echo "<tr>
        <th>Proyecto</th>
        <th>Caso</th>
        <th>FechaRegistro</th>
        <th>GrupoSoporte</th>
        <th>Especialista</th>
        <th>Servicio</th>
        <th>Categoria</th>
        <th>Jerarquia</th>
        <th>EstadoCaso</th>
        <th>FechaRegistroCaso</th>
        <th>FechaAtencionEstimada</th>
        <th>FechaAtencion</th>
        <th>FechaSolucionReal</th>
        <th>FechaSolucionEstimada</th>
        <th>FechaCierre</th>
        <th>NombreCliente</th>
        <th>EstadoAbiertoCerrado</th>
        <th>Asunto</th>
        <th>ItemId</th>
        <th>CreadorCaso</th>
        <th>Canal de ingreso</th> <!-- Reemplaza con los nombres reales de los campos adicionales -->
        <th>Region de radicacion</th> <!-- Reemplaza con los nombres reales de los campos adicionales -->
        <th>Radicado SIB</th> <!-- Reemplaza con los nombres reales de los campos adicionales -->
        <th>Cedula cliente</th>
        <th>Codigo vendedor</th>
        <th>Etapa</th>
        <th>Centro</th>
        <th>Guiainmobiliario</th>
        <th>Correocomercial</th>
      </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row['Proyecto'] . "</td>";
    echo "<td>" . $row['Caso'] . "</td>";
    echo "<td>" . ($row['FechaRegistro'] ? $row['FechaRegistro']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . $row['GrupoSoporte'] . "</td>";
    echo "<td>" . $row['Especialista'] . "</td>";
    echo "<td>" . $row['Servicio'] . "</td>";
    echo "<td>" . $row['Categoria'] . "</td>";
    echo "<td>" . $row['Jerarquia'] . "</td>";
    echo "<td>" . $row['EstadoCaso'] . "</td>";
    echo "<td>" . ($row['FechaRegistroCaso'] ? $row['FechaRegistroCaso']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . ($row['FechaAtencionEstimada'] ? $row['FechaAtencionEstimada']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . ($row['FechaAtencion'] ? $row['FechaAtencion']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . ($row['FechaSolucionReal'] ? $row['FechaSolucionReal']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . ($row['FechaSolucionEstimada'] ? $row['FechaSolucionEstimada']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . ($row['FechaCierre'] ? $row['FechaCierre']->format('Y-m-d H:i:s') : '') . "</td>";
    echo "<td>" . $row['NombreCliente'] . "</td>";
    echo "<td>" . $row['EstadoAbiertoCerrado'] . "</td>";
    echo "<td>" . $row['Asunto'] . "</td>";
    echo "<td>" . $row['ItemId'] . "</td>";
    echo "<td>" . $row['CreadorCaso'] . "</td>";
    echo "<td>" . $row['Canal de ingreso'] . "</td>"; // Reemplaza con los nombres reales de los campos adicionales
    echo "<td>" . $row['Region de radicacion'] . "</td>";
    echo "<td>" . $row['Radicado SIB'] . "</td>"; // Reemplaza con los nombres reales de los campos adicionales
    echo "<td>" . $row['Cedula cliente'] . "</td>";
    echo "<td>" . $row['Codigo vendedor'] . "</td>";
    echo "<td>" . $row['Etapa'] . "</td>";
    echo "<td>" . $row['Centro'] . "</td>";
    echo "<td>" . $row['Guiainmobiliario'] . "</td>";
    echo "<td>" . $row['Correocomercial'] . "</td>";
    echo "</tr>";
} 

echo "</table>";

// Liberar los recursos
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>