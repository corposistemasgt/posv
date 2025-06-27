<?php
require_once "security.php";

class Login
{
    private $db_connection = null;
    public $errors = array();
    public $messages = array();
    public function __construct()
    {
        session_start();
        if (isset($_GET["logout"])) {
            $this->doLogout();
        }
        elseif (isset($_POST["login"])) {
            $this->dologinWithPostData();
        }
    }
    private function dologinWithPostData()
    {
        if (empty($_POST['usuario_users'])) {
            $this->errors[] = "Campo de Usuario Vacio.";
        } elseif (empty($_POST['con_users'])) {
            $this->errors[] = "Campo de Contraseña Vacio.";
        } elseif (empty($_POST['nit_users'])) {
            $this->errors[] = "Campo de Nit Vacio.";
        } elseif (!empty($_POST['usuario_users']) && !empty($_POST['con_users'])&& !empty($_POST['nit_users'])) {
            

            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            if (!$this->db_connection->connect_errno) {
               
                $sql = "SELECT * from tbusuario where nit='".$_POST['nit_users']."'";
                $result_of_login_check = $this->db_connection->query($sql);
                if ($result_of_login_check->num_rows == 1) 
                {
                    $result_row = $result_of_login_check->fetch_object();
                    $_SESSION['bd']=$result_row->base;
                    $_SESSION['us']=$result_row->user;
                    $_SESSION['pa']=$result_row->pass;
                    $this->db_connection = new mysqli(DB_HOST, base64_decode($result_row->user), base64_decode($result_row->pass),"corpo_".base64_decode($result_row->base));
                    if (!$this->db_connection->set_charset("utf8")) {
                        $this->errors[] = $this->db_connection->error;
                    }
                    if (!$this->db_connection->connect_errno) {
                        $vala=base64_encode(base64_encode(base64_encode($_POST['con_users'])));
                        $usuario_users = $this->db_connection->real_escape_string($_POST['usuario_users']);
                        $sql = "SELECT id_users, usuario_users, email_users, con_users,sucursal_users ,cargo_users
                                FROM users
                                WHERE  usuario_users = '" . $usuario_users . "' OR email_users = '" . $usuario_users . "';";
                        $result_of_login_check = $this->db_connection->query($sql);
                        if ($result_of_login_check->num_rows == 1) {
                            $result_row = $result_of_login_check->fetch_object();
                            if (strcmp($vala, $result_row->con_users)===0) 
                            {
                                $_SESSION['prueba']="corpo";
                                $_SESSION['id_users']          = $result_row->id_users;
                                $_SESSION['usuario_users']     = $result_row->usuario_users;
                                $_SESSION['email_users']       = $result_row->email_users;
                                $_SESSION['idsucursal']        = $result_row->sucursal_users;
                                $_SESSION['grupo']             = $result_row->cargo_users;
                                $_SESSION['user_login_status'] = 1;
                                $_SESSION['ruta']=get_url2();
                                $sql = "SELECT * from tbconfiguracion";
                                $query = $this->db_connection->query($sql);
                                $row = $query->fetch_object(); 
                                $_SESSION['requestor']    = $row->requestor;
                                $_SESSION['regimen']      = $row->regimen;
                                $_SESSION['imprimir_codigo']= $row->imprimir_codigo;
                                $_SESSION['vencimientos'] = $row->vencimientos;
                                $_SESSION['genericos']    = $row->genericos;
                                $_SESSION['medidas']      = $row->medidas;
                                $_SESSION['imprimir']     = $row->imprimir_codigo;
                                $_SESSION['rutas']        = $row->rutas;
                                $_SESSION['nit']        = $row->nit;
                                $_SESSION['rutero']       = $row->rutero;
                                $_SESSION['casas']        = $row->casas;
                                $_SESSION['cargar']        = $row->cargar;
                                $_SESSION['sunmi']        = $row->sunmi;
                                $_SESSION['cotizacion_carta']        = $row->cotizacion_carta;
                                $_SESSION['cotizacion_escuela']        = $row->cotizacion_escuela;
                                $_SESSION['comprobante_carta']        = $row->comprobante_carta;
                                $_SESSION['ticket']        = $row->ticket;
                            } else {
                                $this->errors[] = "Usuario y/o contraseña no coinciden.";
                            }
                        } else {
                            $this->errors[] = "Usuario y/o contraseña no coinciden.";
                        }
                    } else {
                        $this->errors[] ="Problema de conexión de base de datos.";
                    }
                
                }
                else
                {
                    $this->errors[] = "NIT Incorrecto.";
                }
                
            } else {
                $this->errors[] ="Problema de conexión de base de datos.";
            }
                
       /*
              $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }
            if (!$this->db_connection->connect_errno) {
                $vala=base64_encode(base64_encode(base64_encode($_POST['con_users'])));
                $usuario_users = $this->db_connection->real_escape_string($_POST['usuario_users']);
                $sql = "SELECT id_users, usuario_users, email_users, con_users,sucursal_users ,cargo_users
                        FROM users
                        WHERE  usuario_users = '" . $usuario_users . "' OR email_users = '" . $usuario_users . "';";
                $result_of_login_check = $this->db_connection->query($sql);
                if ($result_of_login_check->num_rows == 1) {
                    $result_row = $result_of_login_check->fetch_object();
                    if (strcmp($vala, $result_row->con_users)===0) 
                    {
                        $_SESSION['prueba']="corpo";
                        $_SESSION['id_users']          = $result_row->id_users;
                        $_SESSION['usuario_users']     = $result_row->usuario_users;
                        $_SESSION['email_users']       = $result_row->email_users;
                        $_SESSION['idsucursal']        = $result_row->sucursal_users;
                        $_SESSION['grupo']             = $result_row->cargo_users;
                        $_SESSION['user_login_status'] = 1;
                        $_SESSION['ruta']=get_url2();
                        $sql = "SELECT * from tbconfiguracion";
                        $query = $this->db_connection->query($sql);
                        $row = $query->fetch_object(); 
                        $_SESSION['requestor']    = $row->requestor;
                        $_SESSION['regimen']      = $row->regimen;
                        $_SESSION['imprimir_codigo']= $row->imprimir_codigo;
                        $_SESSION['vencimientos'] = $row->vencimientos;
                        $_SESSION['genericos']    = $row->genericos;
                        $_SESSION['medidas']      = $row->medidas;
                        $_SESSION['imprimir']     = $row->imprimir_codigo;
                        $_SESSION['rutas']        = $row->rutas;
                        $_SESSION['nit']        = $row->nit;
                        $_SESSION['rutero']       = $row->rutero;
                        $_SESSION['casas']        = $row->casas;
                        $_SESSION['cargar']        = $row->cargar;
                        $_SESSION['sunmi']        = $row->sunmi;
                        $_SESSION['cotizacion_carta']        = $row->cotizacion_carta;
                        $_SESSION['cotizacion_escuela']        = $row->cotizacion_escuela;
                        $_SESSION['comprobante_carta']        = $row->comprobante_carta;
                    } else {
                        $this->errors[] = "Usuario y/o contraseña no coinciden.";
                    }
                } else {
                    $this->errors[] = "Usuario y/o contraseña no coinciden.";
                }
            } else {
                $this->errors[] ="Problema de conexión de base de datos.";
            }

*/


        }
    }
    public function doLogout()
    {
        $_SESSION = array();
        session_destroy();
        $this->messages[] = "Has sido desconectado.";

    }
    public function isUserLoggedIn()
    {
        if (isset($_SESSION['user_login_status']) and $_SESSION['user_login_status'] == 1) {
            return true;
        }
        return false;
    }
}