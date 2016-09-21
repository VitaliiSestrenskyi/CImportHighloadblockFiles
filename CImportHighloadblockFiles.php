<?php

class CImportHighloadblockFiles{
    protected $highloadblockId;
    protected $xmlStartFileName;

    function __construct($highloadblockId, $xmlStartFileName)
    {
        $this->highloadblockId  = $highloadblockId;
        $this->xmlStartFileName = $xmlStartFileName;
    }

    public function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;

        return $out;
    }

    public function updateHlElement()
    {
        $filePath = $this->getXmlFullFileName();
        if( $this->checkImportFile() )
        {
            $xml = simplexml_load_file( $filePath );
            $arrayXml = json_decode(json_encode((array)$xml), TRUE); // мой массив,  моя прелесть

            foreach ( $arrayXml['ПользовательскиеСправочники']['Справочник']['ЭлементыСправочника']['ЭлементСправочника'] as $key => $val)
            {
                $xmlIdBrand   = $val['Ид'];
                $arrBrandInfo = $val['ЗначенияРеквизитов']['ЗначениеРеквизита'];

                foreach ($arrBrandInfo as $k=>$rekvizit)
                {
                    if( $rekvizit["Наименование"] == "ЛоготипБренда" ) //ЛоготипБренда
                    {
                        $idSavedFile = $this->saveImportFile('/upload/1c_brand', $rekvizit['Значение']); //значения для хайлоада
                        $idHlRecord  = $this->getIdHighloadBlockElement($xmlIdBrand);
                        $data = [
                            "UF_FILE"=>$idSavedFile
                        ];
                        $entity_data_class = $this->getEntityDataClass();
                        $entity_data_class::update($idHlRecord, $data);
                    }
                    elseif( $rekvizit["Наименование"] == "ПромокартинкаБренда" )  //ПромокартинкаБренда
                    {
                        $idSavedFile = $this->saveImportFile('/upload/1c_brand', $rekvizit['Значение']); //значения для хайлоада
                        $idHlRecord  = $this->getIdHighloadBlockElement($xmlIdBrand);

                        $data = [
                            "UF_FILE_PROMO_IMG"=>$idSavedFile
                        ];
                        $entity_data_class = $this->getEntityDataClass();
                        $entity_data_class::update($idHlRecord, $data);
                    }
                }
            }

        }
    }

    public function checkImportFile()
    {
        if(file_exists($this->getXmlFullFileName()))
            return true;
        else
            throw new Bitrix\Main\SystemException('Error with brand xml file');
    }

    public function getXmlFullFileName()
    {
        $dir = new DirectoryIterator( Bitrix\Main\Application::getDocumentRoot() . '/upload/1c_highloadblock/');
        foreach ($dir as $fileinfo)
        {
            if ($fileinfo->isFile())
            {
                if( strstr($fileinfo->getBasename(),$this->xmlStartFileName ) )
                {
                    $filePath = Bitrix\Main\Application::getDocumentRoot() . '/upload/1c_highloadblock/' . $fileinfo->getBasename();
                }
            }
        }

        return $filePath;
    }

    public function saveImportFile($save_path, $filePathXml)
    {
        $arFile      = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/1c_highloadblock/".$filePathXml);
        $resSaveFile = CFile::SaveFile($arFile, $save_path, false, false); //id file

        if($resSaveFile)
            return $resSaveFile;
        else
            throw new Bitrix\Main\SystemException('Error saving file');
    }

    public  function getEntityDataClass()
    {
        CModule::IncludeModule('highloadblock');
        $hlbl = $this->highloadblockId;
        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($hlbl)->fetch();
        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();

        return $entity_data_class;
    }

    public function getIdHighloadBlockElement($UF_XML_ID)
    {
        CModule::IncludeModule('highloadblock');
        $entity_data_class = $this->getEntityDataClass();
        $arData = $entity_data_class::getList(array(
            'select' => array('*'),
            'filter' => array('UF_XML_ID'=>$UF_XML_ID)
        ))->fetch();

        if($arData)
            return $arData['ID'];
    }
}//end Class
?>

<?
<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация xmlns="urn:1C.ru:commerceml_2" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ВерсияСхемы="2.09" ДатаФормирования="2016-09-20T16:12:12" Ид="1">
	<ПользовательскиеСправочники СодержитТолькоИзменения="true">
		<Справочник>
			<Ид>Бренды</Ид>
			<Наименование>Бренды</Наименование>
			<Реквизиты>
				<Реквизит>
					<Ид>Код</Ид>
					<Наименование>Код</Наименование>
					<ТипЗначений>Строка</ТипЗначений>
				</Реквизит>
				<Реквизит>
					<Ид>Наименование</Ид>
					<Наименование>Наименование</Наименование>
					<ТипЗначений>Строка</ТипЗначений>
				</Реквизит>
				<Реквизит>
					<Ид>ЛоготипБренда</Ид>
					<Наименование>Логотип бренда</Наименование>
					<ТипЗначений>Строка</ТипЗначений>
				</Реквизит>
				<Реквизит>
					<Ид>ПромокартинкаБренда</Ид>
					<Наименование>Промокартинка бренда</Наименование>
					<ТипЗначений>Строка</ТипЗначений>
				</Реквизит>
			</Реквизиты>
			<ЭлементыСправочника>
				
				<ЭлементСправочника>
					<Ид>67381929-a41a-11e5-943e-14dae9ef6785</Ид>
					<НомерВерсии>AAAAAABOiq0=</НомерВерсии>
					<ЗначенияРеквизитов>
						<ЗначениеРеквизита>
							<Наименование>Код</Наименование>
							<Значение/>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>Наименование</Наименование>
							<Значение>ELEAF</Значение>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>ЛоготипБренда</Наименование>
							<Значение>import_files/67/67381929-a41a-11e5-943e-14dae9ef6785_2f92173a-9258-4a96-86bd-4539bc62b767.png</Значение>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>ПромокартинкаБренда</Наименование>
							<Значение>import_files/67/67381929-a41a-11e5-943e-14dae9ef6785_1e4a2844-bc22-4f9f-90ae-406dcdf0e8af.jpg</Значение>
						</ЗначениеРеквизита>
					</ЗначенияРеквизитов>
				</ЭлементСправочника>
				<ЭлементСправочника>
					<Ид>e1c58b90-1dc7-11e6-9441-14dae9ef6785</Ид>
					<НомерВерсии>AAAAAABOdY8=</НомерВерсии>
					<ЗначенияРеквизитов>
						<ЗначениеРеквизита>
							<Наименование>Код</Наименование>
							<Значение/>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>Наименование</Наименование>
							<Значение>KANGERTECH</Значение>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>ЛоготипБренда</Наименование>
							<Значение>import_files/e1/e1c58b90-1dc7-11e6-9441-14dae9ef6785_fb4b8bf7-aa75-47ad-be19-a05f87408d1e.jpg</Значение>
						</ЗначениеРеквизита>
					</ЗначенияРеквизитов>
				</ЭлементСправочника>
				<ЭлементСправочника>
					<Ид>baaf663b-c5a0-11e5-943e-14dae9ef6785</Ид>
					<НомерВерсии>AAAAAABOdPk=</НомерВерсии>
					<ЗначенияРеквизитов>
						<ЗначениеРеквизита>
							<Наименование>Код</Наименование>
							<Значение/>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>Наименование</Наименование>
							<Значение>JOYETECH</Значение>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>ЛоготипБренда</Наименование>
							<Значение>import_files/ba/baaf663b-c5a0-11e5-943e-14dae9ef6785_99992f01-b7d3-4e3e-9014-90b2149048ae.png</Значение>
						</ЗначениеРеквизита>
					</ЗначенияРеквизитов>
				</ЭлементСправочника>
				
				
			</ЭлементыСправочника>
		</Справочник>
	</ПользовательскиеСправочники>
</КоммерческаяИнформация>
?>



