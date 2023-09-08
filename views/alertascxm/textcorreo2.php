<!DOCTYPE html>
<html>
<head>
	<title>  </title>
</head>
<body>
	<table id='tblListadoGrupales'>
                        <thead>
                            <tr>
                                <th class='text-center' align='text-center' scope='col' style='background-color: #C6C6C6;'><label style='font-size: 13px; margin: 30px;'>
                                <label style='font-size: 40px; margin: 50px; font-family: "Nunito",sans-serif; color: #1d2d4f;'>CXM</label>
                                </th>
                                <th class='text-center' align='text-center' scope='col' style='background-color: #C6C6C6;'><label style='font-size: 40px; margin: 50px; font-family: "Nunito",sans-serif; color: #1d2d4f;'>Informe de Alertas CX-Management</label></th>
                            </tr>
                            <tr>
                                <th class='text-center' align='text-center' scope='col' style='background-color: #C6C6C6;'>
                                    <label style='font-size: 15px; margin: 50px; font-family: "Nunito",sans-serif; color: #1d2d4f;'>Información:</label>
                                </th>
                                <th class='text-center' align='text-center' scope='col'>              
                                    <label style='font-size: 15px;  margin: 30px; font-family: "Nunito",sans-serif; color: #1d2d4f;'>¡Hola equipo! Te comentamos que nos encantaria saber tú opinión, por eso te invitamos a ingresar a CXM y responder la encuesta en el siguiente link <a href='http://localhost:8080/qa_pruebas/web/index.php/alertascxm/alertaencuesta?id_alerta=".$varIdAlertas."'>Ingresar a la encuesta</a></label>

                                    <hr>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th class='text-center' align='text-center' scope='col' style='background-color: #C6C6C6;'>
                                    <label style='font-size: 15px; margin: 50px; font-family: "Nunito",sans-serif; color: #1d2d4f;'>Datos Alerta:</label>
                                </th>
                                <td class='text-left' align='text-left'>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Fecha de envio: ".$varFechas_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Valorador: ".$varValorador_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Tipo de Alerta: ".$varTipoAlerta_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Programa/Pcrc: ".$varPcrc_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Asunto: ".$varAsuntos_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Comentarios: ".$varComentarios_correo." </p></label></label>
                                    <br>
                                    <label style='font-size: 15px;'><label style='font-size: 15px; font-family: "Nunito",sans-serif; color: #1d2d4f;'><p>* Archivo Adjunto: ".$varArchivo_correo."</p></label></label>
                                </td>
                            </tr>
                            <tr>
                                <td class='text-center' align='text-center' colspan='2' >
                                    <label style='font-size: 12px;  margin: 30px; font-family: "Nunito",sans-serif; color: #1d2d4f;'>© CX-Management 2023 - Desarrollado por Konecta</a></label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
</body>
</html>