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