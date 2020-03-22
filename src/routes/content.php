<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;
//content yazdırma
$app->get('/cs', function (Request $request, Response $response) {
    $token = $request->getParam("token");
    $token = null;

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    $returnArray = array("elma", "armut");;
    if (!is_null($token)) {

        require_once('jwt.php');

        // Get our server-side secret key from a secure location.
        $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

        try {
            $payload = JWT::decode($token, $serverKey, array('HS256'));
            $returnArray = array('userId' => $payload->userId);
            if (isset($payload->exp)) {
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
            }
        } catch (Exception $e) {
            $returnArray = array('error' => $e->getMessage());
        }
    } else {
        $returnArray = array('0' => 'Token bulunamadı');
    }

    // return to caller
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    $lalad = json_encode($returnArray);
    $someArray = json_decode($lalad, true);
    if (isset($returnArray[0])) {
        echo "yok";
    } else {
        $db = new Db();
        try {
            $db = $db->connect();

            $courses = $db->query("SELECT * FROM content")->fetchAll(PDO::FETCH_OBJ);

            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson($courses);
        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        $db = null;
    }
});
// login kontrol
$app->get('/login/ct', function (Request $request, Response $response) {
    $token = $request->getParam("token");
    $token = null;

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    }

    if (!is_null($token)) {

        require_once('jwt.php');

        // Get our server-side secret key from a secure location.
        $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

        try {
            $payload = JWT::decode($token, $serverKey, array('HS256'));
            $returnArray = array('userId' => $payload->userId);
            if (isset($payload->exp)) {
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
            }
        } catch (Exception $e) {
            $returnArray = array('error' => $e->getMessage());
        }
    } else {
        $returnArray = array('error' => 'Token bulunamadı');
    }

    // return to caller
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    echo $jsonEncodedReturnArray;
});

// login olma ve token oluşturma
$app->post('/login', function (Request $request, Response $response) {

    $db = new Db();
    try {
        $db = $db->connect();
        $email = $request->getParam("email");
        $password = $request->getParam("password");
        $courses = $db->query("SELECT * FROM login where email= '$email' and  password= '$password'")->fetchAll(PDO::FETCH_OBJ);

        if ($courses) {
            //print_r ($lalad['0']);
            $lalad = json_encode($courses);
            $someArray = json_decode($lalad, true);
            require_once('jwt.php');

            $userId = $someArray[0]["login_id"];
            $iss = 'deneme_api';
            $aud = 'deneme';
            $name = $someArray[0]["username"];
            $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

            // create a token
            $payloadArray = array();
            $payloadArray['userId'] = $userId;
            if (isset($nbf)) {
                $payloadArray['nbf'] = $nbf;
            }
            if (isset($exp)) {
                $payloadArray['exp'] = $exp;
            }
            $payloadArray['iss'] = $iss;
            $payloadArray['aud'] = $aud;
            $payloadArray['name'] = $name;
            $token = JWT::encode($payloadArray, $serverKey);
            // return to caller

            $returnArray = array();
            $returnArray['token'] = $token;
            $returnArray['cct'] = '1';
            $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
            echo "[" . $jsonEncodedReturnArray . "]";
        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text"  => "Kullanıcı Adı veye Şifre Hatalı",
                        "error_C"  => "1"
                    )
                ));
        }
    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text"  => $e->getMessage(),
                    "code"  => $e->getCode()
                )
            )
        );
    }

    $db = null;
});

// kurs detayi..
$app->get('/c/', function (Request $request, Response $response) {

    $token = $request->getParam("token");
    $token = null;

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    $returnArray = array("elma", "armut");;
    if (!is_null($token)) {

        require_once('jwt.php');

        // Get our server-side secret key from a secure location.
        $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

        try {
            $payload = JWT::decode($token, $serverKey, array('HS256'));
            $returnArray = array('userId' => $payload->userId);
            if (isset($payload->exp)) {
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
            }
        } catch (Exception $e) {
            $returnArray = array('error' => $e->getMessage());
        }
    } else {
        $returnArray = array('0' => 'Token bulunamadı');
    }

    // return to caller
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    $lalad = json_encode($returnArray);
    $someArray = json_decode($lalad, true);
    if (isset($returnArray[0])) {
        echo "yok";
    } else {
        // direkt lik ile çekmek için api/c/2 gibi
       // $id = $request->getAttribute("id");
        $id = $request->getParam("id");
        $db = new Db();
        try {
            $db = $db->connect();
            $course = $db->query("SELECT * FROM content WHERE cont_id = $id")->fetch(PDO::FETCH_OBJ);

            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson($course);
        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        $db = null;
    }
});

// yeni kurs ekle...
$app->post('/c/add', function (Request $request, Response $response) {
    $token = $request->getParam("token");
    $token = null;

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    $returnArray = array("elma", "armut");;
    if (!is_null($token)) {

        require_once('jwt.php');

        // Get our server-side secret key from a secure location.
        $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

        try {
            $payload = JWT::decode($token, $serverKey, array('HS256'));
            $returnArray = array('userId' => $payload->userId);
            if (isset($payload->exp)) {
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
            }
        } catch (Exception $e) {
            $returnArray = array('error' => $e->getMessage());
        }
    } else {
        $returnArray = array('0' => 'Token bulunamadı');
    }

    // return to caller
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    $lalad = json_encode($returnArray);
    $someArray = json_decode($lalad, true);
    if (isset($returnArray[0])) {
        echo "yok";
    } else {
        $name      = $request->getParam("name");
        $surname = $request->getParam("surname");
        $image      = $request->getParam("image");
        $date      = $request->getParam("date");

        $db = new Db();
        try {
            $db = $db->connect();
            $statement = "INSERT INTO content (name, surname, image, date) VALUES(:name, :surname, :image, :date)";
            $prepare = $db->prepare($statement);

            $prepare->bindParam("name", $name);
            $prepare->bindParam("surname", $surname);
            $prepare->bindParam("image", $image);
            $prepare->bindParam("date", $date);

            $course = $prepare->execute();

            if ($course) {
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text"  => "Başarılı bir şekilde eklendi"
                    ));
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text"  => "Ekleme işlemi sırasında bir problem oluştu."
                        )
                    ));
            }
        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        $db = null;
    }
});

// kurs güncelle..
$app->put('/c/update', function (Request $request, Response $response) {

    $token = $request->getParam("token");
    $token = null;
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    $returnArray = array("elma", "armut");;
    if (!is_null($token)) {

        require_once('jwt.php');

        // Get our server-side secret key from a secure location.
        $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

        try {
            $payload = JWT::decode($token, $serverKey, array('HS256'));
            $returnArray = array('userId' => $payload->userId);
            if (isset($payload->exp)) {
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
            }
        } catch (Exception $e) {
            $returnArray = array('error' => $e->getMessage());
        }
    } else {
        $returnArray = array('0' => 'Token bulunamadı');
    }

    // return to caller
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    $lalad = json_encode($returnArray);
    $someArray = json_decode($lalad, true);

    if (isset($returnArray[0])) {
        echo "yok";
    } else {

        $id = $request->getParam("id");
       // $id = $request->getAttribute("id"); //kurs detayında anlattım.
        echo "yunus $id";

        if ($id) {

            $name      = $request->getParam("name");
            $surname = $request->getParam("surname");
            $image      = $request->getParam("image");
            $date      = $request->getParam("date");

            $db = new Db();
            try {
                $db = $db->connect();
                $statement = "UPDATE content SET name = :name, surname = :surname, image = :image, date = :date WHERE cont_id = $id";
                $prepare = $db->prepare($statement);

                $prepare->bindParam("name", $name);
                $prepare->bindParam("surname", $surname);
                $prepare->bindParam("image", $image);
                $date->bindParam("date", $image);

                $course = $prepare->execute();

                if ($course) {
                    return $response
                        ->withStatus(200)
                        ->withHeader("Content-Type", 'application/json')
                        ->withJson(array(
                            "text"  => "Başarılı bir şekilde güncellenmiştir.."
                        ));
                } else {
                    return $response
                        ->withStatus(500)
                        ->withHeader("Content-Type", 'application/json')
                        ->withJson(array(
                            "error" => array(
                                "text"  => "Düzenleme işlemi sırasında bir problem oluştu."
                            )
                        ));
                }
            } catch (PDOException $e) {
                return $response->withJson(
                    array(
                        "error" => array(
                            "text"  => $e->getMessage(),
                            "code"  => $e->getCode()
                        )
                    )
                );
            }
            $db = null;
        } else {
            return $response->withStatus(500)->withJson(
                array(
                    "error" => array(
                        "text"  => "ID bilgisi eksik.."
                    )
                )
            );
        }

    }
});

// content sil..
$app->delete('/c/delete/', function (Request $request, Response $response) {

    $token = $request->getParam("token");
    $token = null;

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    }
    $returnArray = array("elma", "armut");;
    if (!is_null($token)) {

        require_once('jwt.php');

        // Get our server-side secret key from a secure location.
        $serverKey = '5f2b5cdbe5194f10b3241568fe4e2b2495494949849';

        try {
            $payload = JWT::decode($token, $serverKey, array('HS256'));
            $returnArray = array('userId' => $payload->userId);
            if (isset($payload->exp)) {
                $returnArray['exp'] = date(DateTime::ISO8601, $payload->exp);;
            }
        } catch (Exception $e) {
            $returnArray = array('error' => $e->getMessage());
        }
    } else {
        $returnArray = array('0' => 'Token bulunamadı');
    }

    // return to caller
    $jsonEncodedReturnArray = json_encode($returnArray, JSON_PRETTY_PRINT);
    $lalad = json_encode($returnArray);
    $someArray = json_decode($lalad, true);
    if (isset($returnArray[0])) {
        echo "yok";
    } else {

        $id = $request->getParam("id");
        $db = new Db();
        try {
            $db = $db->connect();
            $statement = "DELETE FROM content WHERE cont_id = :id";
            $prepare = $db->prepare($statement);
            $prepare->bindParam("id", $id);

            $course = $prepare->execute();

            if ($course) {
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text"  => "Başarılı bir şekilde silinmiştir.."
                    ));
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text"  => "Silme işlemi sırasında bir problem oluştu."
                        )
                    ));
            }
        } catch (PDOException $e) {
            return $response->withJson(
                array(
                    "error" => array(
                        "text"  => $e->getMessage(),
                        "code"  => $e->getCode()
                    )
                )
            );
        }
        $db = null;

    }























});
