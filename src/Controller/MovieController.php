<?php

namespace App\Controller;

use App\Exceptions\FileUploadException;
use App\Exceptions\NoUploadedFileException;
use App\FlashMessage;
use App\Mapper\MovieMapper;
use App\Movie;
use App\Repository\MovieRepository;
use App\Registry;
use App\Response;
use App\UploadedFileHandler;
use Exception;
use App\Request;
use Webmozart\Assert\Assert;

class MovieController
{
    const MAX_SIZE = 1024 * 1000;
    private MovieRepository $movieRepository;

    public function __construct()
    {
        $mapper = new MovieMapper();
        $this->movieRepository = new MovieRepository($mapper);
    }

    public function list(): Response
    {
        $movies = $this->movieRepository->findAll();

        $logger = Registry::get(Registry::LOGGER);
        $logger->info("s'ha executat una consulta");

        $response = new Response();
        $response->setView("index");
        $response->setLayout("default");
        $response->setData(compact('movies'));

        return $response;
    }

    public function edit(int $id): Response
    {
        $message = "";
        $movie = $this->movieRepository->find($id);
        $data = $movie->toArray();

        if (empty($data))
            throw new \Exception("La pel·lícula seleccionada no existeix");


        $validTypes = ["image/jpeg", "image/jpg", "image/png"];

        $errors = [];

        // per a la vista necessitem saber si s'ha processat el formulari
        if (isPost()) {
            $data["title"] = clean($_POST["title"]);
            $data["overview"] = clean($_POST["overview"]);
            $data["release_date"] = $_POST["release_date"];
            try {
                $uploadedFileHandler = new UploadedFileHandler("poster", $validTypes, self::MAX_SIZE);
                $data["poster"] = $uploadedFileHandler->handle(Movie::POSTER_PATH);

            } catch (NoUploadedFileException $e) {
                // no faig res perquè és una opció vàlida en UPDATE.
            } catch (FileUploadException $e) {
                $errors[] = $e->getMessage();
            }

            if (empty($errors)) {
                try {
                    $movie = Movie::fromArray($data);
                    $this->movieRepository->save($movie);
                    $message = "S'ha actualitzat el registre amb l'ID ({$movie->getId()})";
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }

            }
        }
        $response = new Response();
        $response->setView("movies-edit");
        $response->setLayout("default");
        $response->setData(compact('movie', 'data', 'message'));

        return $response;
    }

    public function view(int $id): Response{
        $movie = $this->movieRepository->find($id);
        $errors = [];
        if(empty($movie))
            array_push($errors,"No se ha encontrado la película indicada");
        $request = new Request();

        $response = new Response();
        $response->setView("movie");
        $response->setLayout("default");
        $response->setData(compact("movie", "errors", "request"));

        return $response;
    }

    public function create(): Response{
        if (isPost())
            die("Aquest pàgina sols admet el mètode GET");

        $data = FlashMessage::get("data", []);

        if(empty($data["title"]))
            $data["title"] = "";

        if(empty($data["release_date"]))
            $data["release_date"] = "";

        if(empty($data["overview"]))
            $data["overview"] = "";

        if(empty($data["poster"]))
            $data["poster"] = "";

        if(empty($data["rating"]))
            $data["rating"] = 0;

        $errors = FlashMessage::get("errors", []);

        $formToken =  bin2hex(random_bytes(16));
        FlashMessage::set("token", $formToken);

        $response = new Response();
        $response->setView("movies-create");
        $response->setLayout("default");
        $response->setData(compact("data", "errors", "formToken"));

        return $response;
    }

    public function createStore(){
        $data["title"] = "";
        $data["release_date"] = "";
        $data["overview"] = "";
        $data["poster"] = "";
        $data["rating"] = 0;

        $validTypes = ["image/jpeg", "image/jpg", "image/png"];

        $errors = [];

        if (!isPost()) {
            die("Aquesta pàgina sols usa el mètode POST");
        }

        $data =  [];

        $token = FlashMessage::get("token");

        try{
            if(validate_string($_POST["title"], 1, 100)){
                $data["title"] = $_POST["title"];
            }
        }catch (Exception $ex){
            $errors[] = "Título".$ex->getMessage();
        }

        try{
            if(validate_string($_POST["overview"], 1, 1000)){
                $data["overview"] = $_POST["overview"];
            }
        }catch (Exception $ex){
            $errors[] = "Overview".$ex->getMessage();
        }

        try{
            Assert::notEmpty($_POST["release_date"]);
            if(validate_date($_POST["release_date"])){
                $data["release_date"] = $_POST["release_date"];
            }else{
                throw new Exception();
            }
        }catch (Exception $ex){
            $errors[] = "Cal indicar una data correcta";
        }

        $ratingTemp = 0;
        try{
            if(array_key_exists("rating", $_POST))
                $ratingTemp = intval($_POST["rating"]);

            Assert::range($ratingTemp,1,6);
            $data["rating"] = $_POST["rating"];

        }catch (Exception $ex){
            $errors[] = "El rating ha de ser un enter entre 1 i 5";
        }

        try {
            $uploadedFileHandler = new UploadedFileHandler("poster", $validTypes, self::MAX_SIZE);
            $data["poster"] = $uploadedFileHandler->handle(Movie::POSTER_PATH);

        } catch (FileUploadException $e) {
            $errors[] = $e->getMessage();
        }

        if (empty($errors)) {
            try{
                $movie = Movie::fromArray($data);
                $this->movieRepository->save($movie);
                $message = "S'ha inserit correctamente";
                FlashMessage::set("message", $message);
                header("Location: ".Registry::get(Registry::ROUTER)->generate("movie_list"));
                exit();
            }catch (\Exception $e){
                $errors[] = "No s'ha pogut inserir el registre";
            }


        }
        FlashMessage::set("data", $data);
        FlashMessage::set("errors", $errors);

        header("Location: ".Registry::get(Registry::ROUTER)->generate("movie_create"));
        exit();
    }

    public function delete(int $id): Response{

        try{
        $movie = $this->movieRepository->find($id);
        $errors = [];
        $message = "";
        if(empty($movie)){
            throw  new Exception();
        }

        }catch (Exception $e){
            array_push($errors,"No se ha encontrado la película indicada");
        }

        if (isPost()) {
            $idTemp = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
            $response = filter_input(INPUT_POST, "response", FILTER_SANITIZE_SPECIAL_CHARS);


            if ($response!=="Sí")
                $errors[] = "L'esborrat ha sigut cancelat per l'usuari";

            if (!empty($idTemp))
                $id = $idTemp;
            else
                throw  new Exception("Invalid ID");
        }

        if (isPost() && empty($errors)) {
            try{
                $this->movieRepository->remove($id);
                var_dump("llego aquí");
                $message = "Se ha eliminado satisfactoriamente la película";
            }catch (Exception $ex){
                $errors [] = "No se ha podido eliminar la película. Sentimos las molestias.";
            }
        }
        $response = new Response();
        $response->setView("movies-delete");
        $response->setLayout("default");
        $response->setData(compact ("errors", "message", "movie", "id"));

        return $response;

    }
}