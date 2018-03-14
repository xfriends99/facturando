<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Datos del usuario</title>
    <!-- FIN DE METADATOS -->

</head>
<body>
<!--CONTENEDOR PADRE-->
<h4 style="text-align:left">Recuperación de Clave</h4>
<table cellpadding="0" cellspacing="0" width="600px">
    <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Clave</th>
    </tr>
    <tr>
        <td>{{$user->name}}</td>
        <td>{{$user->lastname}}</td>
        <td>{{$user->email}}</td>
        <td>{{$user->roles->rol}}</td>
        <td>{{$password}}</td>
    </tr>
</table>
</body>
</html>