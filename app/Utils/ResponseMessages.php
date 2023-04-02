<?php

namespace App\Utils;

class ResponseMessages
{
    const UPLOADED_FILE = "Arquivo enviado com sucesso!";
    const ERROR_MESSAGE = "Ocorreu um erro!";
    const FILE_PROCESSED_SUCCESS = "processamento iniciado, dependendo do tamanho do arquivo isso pode levar alguns minutos";
    const PROCESS_QUEUE_FAIL = "Falha ao tentar processar o arquivo.";
    const NOT_FOUND_OR_DUPLICATED_DATA = "Nenhum novo registro encontrado.";
    const STORE_FILE_FAIL =  "Falha ao armazenar o arquivo.";
    const CATEGORY_NOT_FOUND = "O Arquivo contem categorias não cadastradas.";
    const UPLOAD_FILE_NOT_FOUND = "Nenhum arquivo foi enviado.";
    const PREVIOUSLY_IMPORTED_FILE = "Arquivo já importado anteriormente.";
    const WRONG_FORMAT_FILE = "Arquivo no formato incorreto.";
    const EMPTY_FILE = "Arquivo vazio.";
    const NON_STANDARD_FILE_FORMATTING = "Formatação do arquivo fora do padrão.";
    const FILE_DOCUMENT_IMPORT_EMPTY = "Arquivo não possui documentos a serem importados.";
    const MISSING_DOCUMENTS_PROPERTIES = "Arquivo com documentos sem as propriedades necessárias. Verifique a formatação do arquivo.";
}
