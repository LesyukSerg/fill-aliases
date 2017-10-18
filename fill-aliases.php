<?
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

    function _translit($str)
    {
        $str = strtolower(htmlspecialchars_decode($str));
        $replacement = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'csh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'jo', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'Й' => 'j', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'kh', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'shh', 'Ъ' => '', 'Ь' => '', 'Ы' => 'y', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
            '*' => '_', '_' => '', '/' => '_', '[' => '', ']' => '', '-' => '_', '(' => '', ')' => '', ' ' => '_', ',' => '', '.' => '_', '&' => '', '!' => '', '+' => '', '#' => '', '@' => '', '™' => '', '$' => 's', '`' => '', "'" => '', '"' => ''
        ];
        $s = strtr($str, $replacement);
        $s = str_replace(['___', '__'], '_', $s);

        return $s;
    }

    function getUniqAlias($alias, $number = 0)
    {
        if ($number) {
            $aliasCheck = $alias . '-' . $number;
        } else {
            $aliasCheck = $alias;
        }
        $res = CIBlockElement::GetList([], ["CODE" => $aliasCheck], false, false, ["ID"]);
        $ob = $res->GetNextElement();
        if ($ob) {
            $arFields = $ob->GetFields();
            if (isset($arFields['ID'])) {
                return getUniqAlias($alias, ++$number);
            }
        }

        return $aliasCheck;
    }

    if (isset($_GET['update_ctg'])) {
        echo "Обновление категорий<br>";
        $IBLOCK_ID = 6;
        $arFilter = ['IBLOCK_ID' => $IBLOCK_ID];
        $db_list = CIBlockSection::GetList(['ID' => 'DESC'], $arFilter, true, ["ID", "CODE", "NAME"]);

        while ($item = $db_list->GetNext()) {
            $newCode = _translit($item['NAME']);

            if ($newCode != $item['CODE']) {
                $bs = new CIBlockSection;

                if (!$bs->Update($item['ID'], ["CODE" => $newCode])) {
                    echo $bs->LAST_ERROR;
                }

                echo $item['ID'] . ' ' . $item['NAME'] . ': <br>' . $item['CODE'] . ' = <br>' . $newCode . '<hr>';
            }
        }
    }

    if (isset($_GET['update_prd'])) {
        echo "Обновление товаров<br>";
        $IBLOCK_ID = 6;
        $arFilter = ['IBLOCK_ID' => $IBLOCK_ID];
        $db_list = CIBlockSection::GetList(['ID' => 'DESC'], $arFilter, true, ["ID", "CODE", "NAME"]);


        $arSelect = ["ID", "NAME", "CODE"];
        $arFilter = ["IBLOCK_ID" => $IBLOCK_ID];
        $res = CIBlockElement::GetList([], $arFilter, false, ["nPageSize" => 50], $arSelect);
        while ($prod = $res->GetNextElement()) {
            $item = $prod->GetFields();
            $newCode = _translit($item['NAME']);

            if ($newCode != $item['CODE']) {
                $el = new CIBlockElement;

                if (!$el->Update($item['ID'], ["CODE" => $newCode])) {
                    echo $el->LAST_ERROR;
                    die;
                    $newCode = getUniqAlias($newCode);
                    $el->Update($item['ID'], ["CODE" => $newCode]);
                }

                echo $item['ID'] . ' ' . $item['NAME'] . ': <br>' . $item['CODE'] . ' = <br>' . $newCode . '<hr>';
            }
        }
    }
