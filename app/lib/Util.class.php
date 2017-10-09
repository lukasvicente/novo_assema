<?php

namespace Lib\Funcoes;

class Util {
    #funcao para formatar a data no grid
    #funcao para formatar a data no grid

    static function formatar_data($valor) {
        return FormatDateTime($valor, 7);
    }

#funcao para exibir mensagem de interacao com usuario

    static function msgAlert($class, $method, $param, $msg) {

// Change the value of the outputText field
        echo "<script language='javascript' type='text/javascript'>\n";

        echo "location.href='index.php?class=" . $class . "&method=" . $method . "&" . $param . "&msg=" . $msg . "';\n";

        echo "</script>";
    }

    /**
     * Fun��o para gerar senhas aleat�rias
     *
     * @author    Thiago Belem <contato@thiagobelem.net>
     *
     * @param integer $tamanho Tamanho da senha a ser gerada
     * @param boolean $maiusculas Se ter� letras mai�sculas
     * @param boolean $numeros Se ter� n�meros
     * @param boolean $simbolos Se ter� s�mbolos
     *
     * @return string A senha gerada
     */
    static function gerarCodigoAleatorio($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
        $lmin = 'abcdefghijklmnopqrstuvwxyz';
        $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '1234567890';
        $simb = '!@#$%*-';
        $retorno = '';
        $caracteres = '';
        $caracteres .= $lmin;
        if ($maiusculas)
            $caracteres .= $lmai;
        if ($numeros)
            $caracteres .= $num;
        if ($simbolos)
            $caracteres .= $simb;
        $len = strlen($caracteres);
        for ($n = 1; $n <= $tamanho; $n++) {
            $rand = mt_rand(1, $len);
            $retorno .= $caracteres[$rand - 1];
        }
        return $retorno;
    }
    
    /**
     *
     * @param string $param
     * @return string com texto sem acentuacao
     */
    #funcao remove acentuacao da string
    static function removerAcentuacaoUpper($string) {

        $table = array(
            'Š' => 'S', 'š' => 's', '�?' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z',
            'ž' => 'z', 'Č' => 'C', '�?' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', '�?' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', '�?' => 'I', 'Î' => 'I',
            '�?' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', '�?' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
            'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r',
        );
        // Traduz os caracteres em $string, baseado no vetor $table
        $string = strtr($string, $table);
        // converte para minúsculo
        $string = strtolower($string);
        // remove caracteres indesejáveis (que não estão no padrão)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        // Remove múltiplas ocorrências de hífens ou espaços
        $string = preg_replace("/[\s-]+/", " ", $string);
        // Transforma espaços e underscores em hífens
        $string = preg_replace("/[\s_]/", " ", $string);

        return strtoupper($string);
    }

    /**
     *
     * @param int $time O tempo em segundos
     * @return string O tempo em forma textual
     */
#funcao converter segundos para anos, meses, dias
    static function time1text($time) {
        $response = array();
//    $years = floor($time / 365);
//    $time = $time % 365;
//    $fator = 0;
//    if ($time < 30) {
//        $fator = 0;
//    } else {
//        if ($time < 90) {
//            $fator = 1;
//        } else {
//            if ($time < 150) {
//                $fator = 2;
//            } else {
//                if ($time < 210) {
//                    $fator = 3;
//                } else {
//                    if ($time < 240) {
//                        $fator = 4;
//                    } else {
//                        if ($time < 300) {
//                            $fator = 5;
//                        } else {
//                            if ($time < 360) {
//                                $fator = 6;
//                            } else {
//                                $fator = 7;
//                            }
//                        }
//                    }
//                }
//            }
//        }
//    }
//
//    $months = floor(($time - $fator) / 30);
//    $time = ($time - $fator) % 30;
//    $days = floor($time);
//$time = $time % 86400;

        $years = floor($time / 365);
        $months = floor(($time % 365) / 30);
        $days = (($time % 365) % 30);


        if ($years > 0)
            $response[] = $years . ' ano' . ($years > 1 ? 's' : ' ');
        if ($months > 0)
            $response[] = $months . ' mes' . ($months > 1 ? 'es' : ' ');
        if ($days > 0)
            $response[] = $days . ' dia' . ($days > 1 ? 's' : ' ');
        return implode(', ', $response);
    }

    /**
     *
     * @param int $time O tempo em segundos
     * @return string O tempo em forma textual
     */
#funcao converter segundos para anos, meses, dias, horas, minutos e segundos
    static function time2text($time) {
        $response = array();
        $years = floor($time / (86400 * 365));
        $time = $time % (86400 * 365);
        $months = floor($time / (86400 * 30));
        $time = $time % (86400 * 30);
        $days = floor($time / 86400);
        $time = $time % 86400;
        $hours = floor($time / (3600));
        $time = $time % 3600;
        $minutes = floor($time / 60);
        $seconds = $time % 60;
        if ($years > 0)
            $response[] = $years . ' ano' . ($years > 1 ? 's' : ' ');
        if ($months > 0)
            $response[] = $months . ' mes' . ($months > 1 ? 'es' : ' ');
        if ($days > 0)
            $response[] = $days . ' dia' . ($days > 1 ? 's' : ' ');
        if ($hours > 0)
            $response[] = $hours . ' hora' . ($hours > 1 ? 's' : ' ');
        if ($minutes > 0)
            $response[] = $minutes . ' minuto' . ($minutes > 1 ? 's' : ' ');
        if ($seconds > 0)
            $response[] = $seconds . ' segundo' . ($seconds > 1 ? 's' : ' ');
        return implode(', ', $response);
    }

    static function diasDeDiferenca($dataInicial, $dataFinal) {
        if ($dataInicial && $dataFinal) {
            $vetorDataInicial = explode('/', $dataInicial);
            $timeInicial = mktime(0, 0, 0, $vetorDataInicial[1], $vetorDataInicial[0], $vetorDataInicial[2]);
            $vetorDataFinal = explode('/', $dataFinal);
            $timeFinal = mktime(0, 0, 0, $vetorDataFinal[1], $vetorDataFinal[0], $vetorDataFinal[2]);
// CALCULA A DIFERENÇA DE SEGUNDOS ENTRE AS DUAS DATAS:
            $diferenca = $timeFinal - $timeInicial;
            $dias = (int) floor($diferenca / (60 * 60 * 24));
        } else {
            $dias = 0;
        }
        return $dias;
    }

//calcular idade
    static function calcularIdade($data_nasc) {

        return floor((time() - strtotime($data_nasc) ) / 31556926);
    }

    static function formatar_hora($valor) {
        $hora = substr($valor, 0, 2) . ":" . substr($valor, 3, 2);
//return strftime('%H:%M', $valor);
        return $hora;
//return $valor;
    }

    static function formatar_hora2($valor) {
        $hora = substr($valor, 11, 2) . ":" . substr($valor, 14, 2);
//return strftime('%H:%M', $valor);
        return $hora;
//return $valor;
    }

    static function GravarDataPostgres($data) {
        $tmp = str_replace("'", "''", $data);
        if ($tmp == "") {
            return "NULL";
        } else {
            $dataemvetor = explode("/", $data);
            $dataatual = mktime(0, 0, 0, $dataemvetor[1], $dataemvetor[0], $dataemvetor[2]);
            if (strcasecmp(date("Y-m-d", $dataatual), "1969-12-31") == 0) {
                return "NULL";
            } else {
                return date("'Y-m-d'", $dataatual);
            }
#     return date("Y-m-d", $dataatual);
        }
    }

    static function FormatarDataPostgres($data) {
        $dataemvetor = explode("-", $data);
        $dataatual = mktime(0, 0, 0, $dataemvetor[1], $dataemvetor[0], $dataemvetor[2]);
        return date("d/m/Y", $dataatual);
    }

    static function FormatDateTime($ts, $namedformat) {
        $separador = "/";
        $formato = "dd/mm/yyyy";
        $DefDateFormat = str_replace("yyyy", "%Y", $formato);
        $DefDateFormat = str_replace("mm", "%m", $DefDateFormat);
        $DefDateFormat = str_replace("dd", "%d", $DefDateFormat);
        if (is_numeric($ts)) { // timestamp
            switch (strlen($ts)) {
                case 14:
                    $patt = '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
                    break;
                case 12:
                    $patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
                    break;
                case 10:
                    $patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
                    break;
                case 8:
                    $patt = '/(\d{4})(\d{2})(\d{2})/';
                    break;
                case 6:
                    $patt = '/(\d{2})(\d{2})(\d{2})/';
                    break;
                case 4:
                    $patt = '/(\d{2})(\d{2})/';
                    break;
                case 2:
                    $patt = '/(\d{2})/';
                    break;
                default:
                    return $ts;
            }
            if ((isset($patt)) && (preg_match($patt, $ts, $matches))) {
                $year = $matches[1];
                $month = @$matches[2];
                $day = @$matches[3];
                $hour = @$matches[4];
                $min = @$matches[5];
                $sec = @$matches[6];
            }
            if (($namedformat == 0) && (strlen($ts) < 10))
                $namedformat = 2;
        }
        elseif (is_string($ts)) {
            if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) { // datetime
                $year = $matches[1];
                $month = $matches[2];
                $day = $matches[3];
                $hour = $matches[4];
                $min = $matches[5];
                $sec = $matches[6];
            } elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $ts, $matches)) { // date
                $year = $matches[1];
                $month = $matches[2];
                $day = $matches[3];
                if ($namedformat == 0)
                    $namedformat = 2;
            }
            elseif (preg_match('/(^|\s)(\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) { // time
                $hour = $matches[2];
                $min = $matches[3];
                $sec = $matches[4];
                if (($namedformat == 0) || ($namedformat == 1))
                    $namedformat = 3;
                if ($namedformat == 2)
                    $namedformat = 4;
            }
            else {
                return $ts;
            }
        } else {
            return $ts;
        }
        if (!isset($year))
            $year = 0; // dummy value for times
        if (!isset($month))
            $month = 1;
        if (!isset($day))
            $day = 1;
        if (!isset($hour))
            $hour = 0;
        if (!isset($min))
            $min = 0;
        if (!isset($sec))
            $sec = 0;
        $uts = @mktime($hour, $min, $sec, $month, $day, $year);
        if ($uts < 0) { // failed to convert
            $year = substr_replace("0000", $year, -1 * strlen($year));
            $month = substr_replace("00", $month, -1 * strlen($month));
            $day = substr_replace("00", $day, -1 * strlen($day));
            $hour = substr_replace("00", $hour, -1 * strlen($hour));
            $min = substr_replace("00", $min, -1 * strlen($min));
            $sec = substr_replace("00", $sec, -1 * strlen($sec));
            $DefDateFormat = str_replace("yyyy", $year, DEFAULT_DATE_FORMAT);
            $DefDateFormat = str_replace("mm", $month, $DefDateFormat);
            $DefDateFormat = str_replace("dd", $day, $DefDateFormat);
            switch ($namedformat) {
                case 0:
                    return $DefDateFormat . " $hour:$min:$sec";
                    break;
                case 1://unsupported, return general date
                    return $DefDateFormat . " $hour:$min:$sec";
                    break;
                case 2:
                    return $DefDateFormat;
                    break;
                case 3:
                    if (intval($hour) == 0)
                        return "12:$min:$sec AM";
                    elseif (intval($hour) > 0 && intval($hour) < 12)
                        return "$hour:$min:$sec AM";
                    elseif (intval($hour) == 12)
                        return "$hour:$min:$sec PM";
                    elseif (intval($hour) > 12 && intval($hour) <= 23)
                        return (intval($hour) - 12) . ":$min:$sec PM";
                    else
                        return "$hour:$min:$sec";
                    break;
                case 4:
                    return "$hour:$min:$sec";
                    break;
                case 5:
                    return "$year" . $separador . "$month" . $separador . "$day";
                    break;
                case 6:
                    return "$month" . $separador . "$day" . $separador . "$year";
                    break;
                case 7:
                    return "$day" . $separador . "$month" . $separador . "$year";
                    break;
                case 8:
                    return "$year" . $separador . "$day" . $separador . "$month";
                    break;
            }
        } else {
            switch ($namedformat) {
                case 0:
                    return strftime($DefDateFormat . " %H:%M:%S", $uts);
                    break;
                case 1:
                    return strftime("%A, %B %d, %Y", $uts);
                    break;
                case 2:
                    return strftime($DefDateFormat, $uts);
                    break;
                case 3:
                    return strftime("%I:%M:%S %p", $uts);
                    break;
                case 4:
                    return strftime("%H:%M:%S", $uts);
                    break;
                case 5:
                    return strftime("%Y" . $separador . "%m" . $separador . "%d", $uts);
                    break;
                case 6:
                    return strftime("%m" . $separador . "%d" . $separador . "%Y", $uts);
                    break;
                case 7:
                    return strftime("%d" . $separador . "%m" . $separador . "%Y", $uts);
                    break;
                case 8:
                    return strftime("%Y" . $separador . "%d" . $separador . "%m", $uts);
                    break;
            }
        }
    }

    static function FormatDateTime_old($ts, $namedformat) {
        define(EW_DATE_SEPARATOR, "/", true);
        define(DEFAULT_DATE_FORMAT, "dd/mm/yyyy", true);

        $DefDateFormat = str_replace("yyyy", "%Y", DEFAULT_DATE_FORMAT);
        $DefDateFormat = str_replace("mm", "%m", $DefDateFormat);
        $DefDateFormat = str_replace("dd", "%d", $DefDateFormat);
        if (is_numeric($ts)) { // timestamp
            switch (strlen($ts)) {
                case 14:
                    $patt = '/(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
                    break;
                case 12:
                    $patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
                    break;
                case 10:
                    $patt = '/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/';
                    break;
                case 8:
                    $patt = '/(\d{4})(\d{2})(\d{2})/';
                    break;
                case 6:
                    $patt = '/(\d{2})(\d{2})(\d{2})/';
                    break;
                case 4:
                    $patt = '/(\d{2})(\d{2})/';
                    break;
                case 2:
                    $patt = '/(\d{2})/';
                    break;
                default:
                    return $ts;
            }
            if ((isset($patt)) && (preg_match($patt, $ts, $matches))) {
                $year = $matches[1];
                $month = @$matches[2];
                $day = @$matches[3];
                $hour = @$matches[4];
                $min = @$matches[5];
                $sec = @$matches[6];
            }
            if (($namedformat == 0) && (strlen($ts) < 10))
                $namedformat = 2;
        }
        elseif (is_string($ts)) {
            if (preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) { // datetime
                $year = $matches[1];
                $month = $matches[2];
                $day = $matches[3];
                $hour = $matches[4];
                $min = $matches[5];
                $sec = $matches[6];
            } elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $ts, $matches)) { // date
                $year = $matches[1];
                $month = $matches[2];
                $day = $matches[3];
                if ($namedformat == 0)
                    $namedformat = 2;
            }
            elseif (preg_match('/(^|\s)(\d{2}):(\d{2}):(\d{2})/', $ts, $matches)) { // time
                $hour = $matches[2];
                $min = $matches[3];
                $sec = $matches[4];
                if (($namedformat == 0) || ($namedformat == 1))
                    $namedformat = 3;
                if ($namedformat == 2)
                    $namedformat = 4;
            }
            else {
                return $ts;
            }
        } else {
            return $ts;
        }
        if (!isset($year))
            $year = 0; // dummy value for times
        if (!isset($month))
            $month = 1;
        if (!isset($day))
            $day = 1;
        if (!isset($hour))
            $hour = 0;
        if (!isset($min))
            $min = 0;
        if (!isset($sec))
            $sec = 0;
        $uts = @mktime($hour, $min, $sec, $month, $day, $year);
        if ($uts < 0) { // failed to convert
            $year = substr_replace("0000", $year, -1 * strlen($year));
            $month = substr_replace("00", $month, -1 * strlen($month));
            $day = substr_replace("00", $day, -1 * strlen($day));
            $hour = substr_replace("00", $hour, -1 * strlen($hour));
            $min = substr_replace("00", $min, -1 * strlen($min));
            $sec = substr_replace("00", $sec, -1 * strlen($sec));
            $DefDateFormat = str_replace("yyyy", $year, DEFAULT_DATE_FORMAT);
            $DefDateFormat = str_replace("mm", $month, $DefDateFormat);
            $DefDateFormat = str_replace("dd", $day, $DefDateFormat);
            switch ($namedformat) {
                case 0:
                    return $DefDateFormat . " $hour:$min:$sec";
                    break;
                case 1://unsupported, return general date
                    return $DefDateFormat . " $hour:$min:$sec";
                    break;
                case 2:
                    return $DefDateFormat;
                    break;
                case 3:
                    if (intval($hour) == 0)
                        return "12:$min:$sec AM";
                    elseif (intval($hour) > 0 && intval($hour) < 12)
                        return "$hour:$min:$sec AM";
                    elseif (intval($hour) == 12)
                        return "$hour:$min:$sec PM";
                    elseif (intval($hour) > 12 && intval($hour) <= 23)
                        return (intval($hour) - 12) . ":$min:$sec PM";
                    else
                        return "$hour:$min:$sec";
                    break;
                case 4:
                    return "$hour:$min:$sec";
                    break;
                case 5:
                    return "$year" . EW_DATE_SEPARATOR . "$month" . EW_DATE_SEPARATOR . "$day";
                    break;
                case 6:
                    return "$month" . EW_DATE_SEPARATOR . "$day" . EW_DATE_SEPARATOR . "$year";
                    break;
                case 7:
                    return "$day" . EW_DATE_SEPARATOR . "$month" . EW_DATE_SEPARATOR . "$year";
                    break;
            }
        } else {
            switch ($namedformat) {
                case 0:
                    return strftime($DefDateFormat . " %H:%M:%S", $uts);
                    break;
                case 1:
                    return strftime("%A, %B %d, %Y", $uts);
                    break;
                case 2:
                    return strftime($DefDateFormat, $uts);
                    break;
                case 3:
                    return strftime("%I:%M:%S %p", $uts);
                    break;
                case 4:
                    return strftime("%H:%M:%S", $uts);
                    break;
                case 5:
                    return strftime("%Y" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%d", $uts);
                    break;
                case 6:
                    return strftime("%m" . EW_DATE_SEPARATOR . "%d" . EW_DATE_SEPARATOR . "%Y", $uts);
                    break;
                case 7:
                    return strftime("%d" . EW_DATE_SEPARATOR . "%m" . EW_DATE_SEPARATOR . "%Y", $uts);
                    break;
            }
        }
    }

    static function dataExtenso($data) {
// leitura das datas
        $dia = date($data, 'd');
        $mes = date($data, 'm');
        $ano = date($data, 'Y');
        $semana = date($data, 'w');


// configuração mes

        switch ($mes) {

            case 1: $mes = "JANEIRO";
                break;
            case 2: $mes = "FEVEREIRO";
                break;
            case 3: $mes = "MARÇO";
                break;
            case 4: $mes = "ABRIL";
                break;
            case 5: $mes = "MAIO";
                break;
            case 6: $mes = "JUNHO";
                break;
            case 7: $mes = "JULHO";
                break;
            case 8: $mes = "AGOSTO";
                break;
            case 9: $mes = "SETEMBRO";
                break;
            case 10: $mes = "OUTUBRO";
                break;
            case 11: $mes = "NOVEMBRO";
                break;
            case 12: $mes = "DEZEMBRO";
                break;
        }


// configuração semana

        switch ($semana) {

            case 0: $semana = "DOMINGO";
                break;
            case 1: $semana = "SEGUNDA FEIRA";
                break;
            case 2: $semana = "TERÇA-FEIRA";
                break;
            case 3: $semana = "QUARTA-FEIRA";
                break;
            case 4: $semana = "QUINTA-FEIRA";
                break;
            case 5: $semana = "SEXTA-FEIRA";
                break;
            case 6: $semana = "S�?BADO";
                break;
        }

        return "$dia DE $mes DE $ano";
    }

    static function retornaMes($param) {
        switch ($param) {


            case 1: $mes = "Janeiro";
                break;
            case 2: $mes = "Fevereiro";
                break;
            case 3: $mes = "Março";
                break;
            case 4: $mes = "Abril";
                break;
            case 5: $mes = "Maio";
                break;
            case 6: $mes = "Junho";
                break;
            case 7: $mes = "Julho";
                break;
            case 8: $mes = "Agosto";
                break;
            case 9: $mes = "Setembro";
                break;
            case 10: $mes = "Outubro";
                break;
            case 11: $mes = "Novembro";
                break;
            case 12: $mes = "Dezembro";
                break;
        }
        return $mes;
    }
    
    static function retornaMesBimestre($param) {
        switch ($param) {

            case 1: $mes = "Janeiro e Fevereiro";
                break;
            case 2: $mes = "Janeiro e Fevereiro";
                break;
            case 3: $mes = "Mar�o e Abril";
                break;
            case 4:  $mes = "Mar�o e Abril";
                break;
            case 5: $mes = "Maio e Junho";
                break;
            case 6: $mes = "Maio e Junho";
                break;
            case 7: $mes = "Julho e Agosto";
                break;
            case 8: $mes = "Julho e Agosto";
                break;
            case 9: $mes = "Setembro e Outubro";
                break;
            case 10: $mes = "Setembro e Outubro";
                break;
            case 11: $mes = "Novembro e Dezembro";
                break;
            case 12: $mes = "Novembro e Dezembro";
                break;
        }
        return $mes;
    }

    static function defineRangerMes($mes1, $ano) {
        switch ($mes1) {
            case 0: $mes = " start='01/01/" . $ano . "' end='31/12/" . $ano . "' label='Meses' ";
                break;
            case 1: $mes = " start='01/01/" . $ano . "' end='31/01/" . $ano . "' label='Jan' ";
                break;
            case 2: $mes = " start='01/02/" . $ano . "' end='28/02/" . $ano . "' label='Fev' ";
                break;
            case 3: $mes = " start='01/03/" . $ano . "' end='31/03/" . $ano . "' label='Mar' ";
                break;
            case 4: $mes = " start='01/04/" . $ano . "' end='30/04/" . $ano . "' label='Abr' ";
                break;
            case 5: $mes = " start='01/05/" . $ano . "' end='31/05/" . $ano . "' label='Mai' ";
                break;
            case 6: $mes = " start='01/06/" . $ano . "' end='30/06/" . $ano . "' label='Jun' ";
                break;
            case 7: $mes = " start='01/07/" . $ano . "' end='31/07/" . $ano . "' label='Jul' ";
                break;
            case 8: $mes = " start='01/08/" . $ano . "' end='31/08/" . $ano . "' label='Ago' ";
                break;
            case 9: $mes = " start='01/09/" . $ano . "' end='30/09/" . $ano . "' label='Set' ";
                break;
            case 10: $mes = " start='01/10/" . $ano . "' end='31/10/" . $ano . "' label='Out' ";
                break;
            case 11: $mes = " start='01/11/" . $ano . "' end='30/11/" . $ano . "' label='Nov' ";
                break;
            case 12: $mes = " start='01/12/" . $ano . "' end='31/12/" . $ano . "' label='Dez' ";
                break;
        }
        return $mes;
    }

    static function validaCPF($cpf) {

        $status = false;

        if (!is_numeric($cpf)) {
            $status = false;
        } else {

            /* aqui ele verifica se todos os números digitados são iguais, caso sejam, faz o mesmo que na condição anterior */

            if (($cpf == '11111111111') || ($cpf == '22222222222') ||
                    ($cpf == '33333333333') || ($cpf == '44444444444') ||
                    ($cpf == '55555555555') || ($cpf == '66666666666') ||
                    ($cpf == '77777777777') || ($cpf == '88888888888') ||
                    ($cpf == '99999999999') || ($cpf == '00000000000')) {
                $status = false;
            } else {

                /* se todos os testes anteriores retonaram true, então será iniciada a verificação dos números */
                /* primeiro o script vai pegar o numero do dígito verificador */

                $dv_informado = substr($cpf, 9, 2);
                for ($i = 0; $i <= 8; $i++) {
                    $digito[$i] = substr($cpf, $i, 1);
                }

                /* Agora será calculado o valor do décimo dígito de verificação */

                $posicao = 10;
                $soma = 0;
                for ($i = 0; $i <= 8; $i++) {
                    $soma = $soma + $digito[$i] * $posicao;
                    $posicao = $posicao - 1;
                }
                $digito[9] = $soma % 11;
                if ($digito[9] < 2) {
                    $digito[9] = 0;
                } else {
                    $digito[9] = 11 - $digito[9];
                }

                /* Agora será calculado o valor do décimo primeiro dígito de verificação */

                $posicao = 11;
                $soma = 0;

                for ($i = 0; $i <= 9; $i++) {
                    $soma = $soma + $digito[$i] * $posicao;
                    $posicao = $posicao - 1;
                }
                $digito[10] = $soma % 11;
                if ($digito[10] < 2) {
                    $digito[10] = 0;
                } else {
                    $digito[10] = 11 - $digito[10];
                }

                /* Nessa parte do script será verificado se o dígito verificador é igual ao informado pelo usuário */

                $dv = $digito[9] * 10 + $digito[10];
                if ($dv != $dv_informado) {
                    $status = false;
                } else
                    $status = true;
            }
        }
        return $status;
    }

    static function validaCNPJ($cnpj) {
        if (strlen($cnpj) <> 14)
            return false;
        $soma1 = ($cnpj[0] * 5) +
                ($cnpj[1] * 4) +
                ($cnpj[2] * 3) +
                ($cnpj[3] * 2) +
                ($cnpj[4] * 9) +
                ($cnpj[5] * 8) +
                ($cnpj[6] * 7) +
                ($cnpj[7] * 6) +
                ($cnpj[8] * 5) +
                ($cnpj[9] * 4) +
                ($cnpj[10] * 3) +
                ($cnpj[11] * 2);
        $resto = $soma1 % 11;
        $digito1 = $resto < 2 ? 0 : 11 - $resto;

        $soma2 = ($cnpj[0] * 6) +
                ($cnpj[1] * 5) +
                ($cnpj[2] * 4) +
                ($cnpj[3] * 3) +
                ($cnpj[4] * 2) +
                ($cnpj[5] * 9) +
                ($cnpj[6] * 8) +
                ($cnpj[7] * 7) +
                ($cnpj[8] * 6) +
                ($cnpj[9] * 5) +
                ($cnpj[10] * 4) +
                ($cnpj[11] * 3) +
                ($cnpj[12] * 2);
        $resto = $soma2 % 11;
        $digito2 = $resto < 2 ? 0 : 11 - $resto;
        return (($cnpj[12] == $digito1) && ($cnpj[13] == $digito2));
    }

    static function valorPorExtenso($valor = 0) {
        $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
            "quatrilhões");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
            "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
            "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
            "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
            "sete", "oito", "nove");

        $z = 0;

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for ($i = 0; $i < count($inteiro); $i++)
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];

// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                    $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++;
            elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        return($rt ? $rt : "zero");
    }

    static function diasPorExtenso($valor = 0) {
        $singular = array("centavo", "dias", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
        $plural = array("centavos", "dias", "mil", "milhões", "bilhões", "trilhões",
            "quatrilhões");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
            "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
            "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
            "dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
            "sete", "oito", "nove");

        $z = 0;

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        for ($i = 0; $i < count($inteiro); $i++)
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];

// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
        for ($i = 0; $i < count($inteiro); $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                    $ru) ? " e " : "") . $ru;
            $t = count($inteiro) - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000")
                $z++;
            elseif ($z > 0)
                $z--;
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= (($z > 1) ? " de " : "") . $plural[$t];
            if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        return($rt ? $rt : "zero");
    }

    static function pegaMac() {

        exec("ipconfig /all", $output);
        foreach ($output as $line) {
            if (preg_match("/(.*)Endereço físico(.*)/", $line)) {
                $mac = $line;
                $mac = str_replace("Endereço físico . . . . . . . . . . :", "", $mac);
            }
        }
        return $mac;
    }

    static function retornaAnocomZero() {
        $itens = array();
        $itens['0'] = 'TODOS';
        for ($index = (date('Y')+1); $index > 2009; $index--) {
            $itens[$index] = $index;
        }

        return $itens;
    }

    static function retornaAnocomZeroPPA() {
        $itens = array();
        $itens['0'] = 'TODOS';
        for ($index = (date('Y') + 4); $index > 2009; $index--) {
            $itens[$index] = $index;
        }

        return $itens;
    }

    static function retornaAnosemZero() {
        $itens = array();
        $itens['2017'] = '2017';
        $itens['2016'] = '2016';
        $itens['2015'] = '2015';
        $itens['2014'] = '2014';
        $itens['2013'] = '2013';
        $itens['2012'] = '2012';
        $itens['2011'] = '2011';
        $itens['2010'] = '2010';

        return $itens;
    }

    static function formatar_moeda($valor) {
        return number_format($valor, 2, ',', '.');
    }

    static function bissexto($ano) {
        $bissexto = false;
// Divisível por 4 e não divisível por 100 ou divisível por 400
        if ((($ano % 4) == 0 && ($ano % 100) != 0) || ($ano % 400) == 0) {
            $bissexto = true;
        }

        return $bissexto;
    }

}

?>