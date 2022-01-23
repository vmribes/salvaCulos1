<?php

use App\Exceptions\RequiredValidationException;
use App\Exceptions\TooLongValidationException;
use App\Exceptions\TooShortValidationException;
use App\Registry;

require_once "src/Exceptions/ValidationException.php";
require_once "src/Exceptions/RequiredValidationException.php";
require_once "src/Exceptions/TooShortValidationException.php";
require_once "src/Exceptions/TooLongValidationException.php";


function clean(string $value): string
{
    $value = trim($value);
    return htmlspecialchars($value);
}

function isPost(): bool
{
    return $_SERVER["REQUEST_METHOD"] === "POST";
}

function validate_string(string $string, int $minLength = 1,
                         int $maxLength = 50000): bool
{
    if (empty($string))
        throw new RequiredValidationException(" este campo es requerido");
    if (strlen($string) < $minLength)
        throw new TooShortValidationException(" este campo es demasiado corto");
    if (strlen($string) > $maxLength)
        throw new TooLongValidationException(" este campo es demasiado largo");

    return true;
}



function getFileExtension(string $filename): string
{
    $mime = "";
    try {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        if ($mime === false)
            throw new Exception();
    } // return mime-type extension
    finally {
        finfo_close($finfo);
    }
    return $mime;
}

function validate_phone(string $phone):bool {
    if (!preg_match("/^\d{9}$/", $phone))
        throw new InvalidPhoneValidationException("Telèfon invàlid");

    return true;
}

function validate_email(string $email): bool {
    if (empty(filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL))) {
        throw new InvalidEmailValidationException("Invalid email");
    }
    return true;
}

// compare if the current value in the selected array
function is_selected(string $value, array $array): bool
{
    if (in_array($value, $array))
        return true;
    return false;
}

function validate_date(string $date): bool {

    if (DateTime::createFromFormat("Y-m-d", $date)===false)
        return false;

    $errors = DateTime::getLastErrors();

    //var_dump($errors);
    if (count($errors["warnings"])>0 || count($errors["errors"])>0)
        return false;

    return true;
}
function validate_elements_in_array_keys(array $needle, array $haystack): bool {
    $diff =  ((!array_diff_key(array_flip($needle), $haystack)));
    if (!$diff)
        throw new InvalidKeyValidationException("Invalid element");

    return $diff;
}

function conectar($userId, $userName)
{
    $_SESSION['userId'] = $userId;
    setcookie("last_used_name", $userName, mktime() . time() + 60 * 60 * 24 * 30);
    $_SESSION['expireSession'] = time() + (1 * 5);
    header("Location: ".Registry::get(Registry::ROUTER)->generate("movie_list"));
    exit;
}