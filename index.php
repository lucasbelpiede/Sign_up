<?php
    //Conexão com o Banco de Dados
    include('connection.php');

    if(isset($_POST['submitxml'])){
        $xmlfile = $_FILES['selectxml'];
        $fileextension = explode('.',$xmlfile['name']);

        //Validação da Extensão do Arquivo
        if($fileextension[sizeof($fileextension)-1] != 'xml'){
            echo('<h3 align="center" style="color: red;">Apenas Arquivos .xml são Aceitos</h3>');
        }
        else{
            $filecontent = simplexml_load_file($xmlfile['tmp_name']);
            
            if(isset($filecontent->NFe)){
                $filevalidation = $filecontent->NFe->infNFe;
            }
            else{
                $filevalidation = $filecontent->infNFe;
            }
            
            //Validação do CNPJ da Nota Fiscal
            if($filevalidation->emit->CNPJ == '09066241000884'){
                
                //Validação do Protocolo de Autorização da Nota Fiscal
                if(isset($filecontent->protNFe->infProt->nProt)){
                    $id = $filevalidation['Id'];
                    $date = $filevalidation->ide->dhEmi;
                    $recipient = $filevalidation->dest->saveXML();
                    $total = $filevalidation->total->saveXML();

                    move_uploaded_file($xmlfile['tmp_name'],__DIR__.'/xml uploads/'.$xmlfile['name']);

                    //Armazenamento Dados da Nota Fiscal no Banco de Dados
                    $connect->query("INSERT INTO invoice (id, date, recipient, total) VALUES ('$id', '$date', '$recipient', '$total')");
                    
                    //Exibição dos Dados da Nota Fiscal no Banco de Dados
                    echo '<h3 align="center" style="color: green;">Número da Nota Fiscal: '.$id.'</h3>';
                    echo '<h3 align="center" style="color: green;">Data da Nota Fiscal: '.$date.'</h3>';
                    echo '<h3 align="center" style="color: green;">Dados Completos do Destinatário: '.$recipient.'</h3>';
                    echo '<h3 align="center" style="color: green;">Valor Total da Nota Fiscal: '.$total.'</h3>';
                }else{
                    echo('<h3 align="center" style="color: red;">Nota Fiscal sem Protocolo de Autorização</h3>');
                }
            }else{
                echo('<h3 align="center" style="color: red;">CNPJ da Nota Fiscal Inválido</h3>');
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Notas Fiscais do Cliente</title>
</head>
<body style="background-color: gray;">
    <!--Tela de Upload de Arquivos .xml-->
    <h1 align="center" style="color: aqua;">Notas Fiscais do Cliente</h1>
    <h2 align="center" style="color: aqua;">Selecione Apenas Arquivos .xml</h2>
    
    <div align="center">
        <form method="post" enctype="multipart/form-data" style="color: aqua;">
            <input type="file" name="selectxml">
            <input type="submit" name="submitxml" value="Enviar">   
        </form>
    </div>
</body>
</html>