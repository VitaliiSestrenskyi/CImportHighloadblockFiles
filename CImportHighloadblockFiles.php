<?php

class CImportHighloadblockFiles{
    protected $highloadblockId;
    protected $xmlStartFileName;

    function __construct($highloadblockId, $xmlStartFileName)
    {
        $this->highloadblockId  = $highloadblockId;
        $this->xmlStartFileName = $xmlStartFileName;
    }

    public function updateHlElement()
    {
        $filePath = $this->getXmlFullFileName();
        if( $this->checkImportFile() )
        {
            $xml = simplexml_load_file( $filePath );
            foreach ($xml->{'ПользовательскиеСправочники'}->{'Справочник'}->{'ЭлементыСправочника'} as $key => $val)
            {
                $xmlIdBrand   = $val->{'ЭлементСправочника'}->{'Ид'};
                $arrBrandInfo = $val->{'ЭлементСправочника'}->{'ЗначенияРеквизитов'}->{'ЗначениеРеквизита'};
                $importArr    = (array)$arrBrandInfo[1]->{'Значение'};

                $idSavedFile = $this->saveImportFile('/upload/1c_brand', $importArr[0]); //значения для хайлоада
                $idHlRecord  = $this->getIdHighloadBlockElement($xmlIdBrand);

                $data = [
                    "UF_FILE"=>$idSavedFile
                ];
                $entity_data_class = $this->getEntityDataClass();
                $result = $entity_data_class::update($idHlRecord, $data);

                if($result->isSuccess())
                {
                    return $result->getId();
                }
                else
                {
                    return false;
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
        $arrayFromObj = (array)$UF_XML_ID[0];
        $arData = $entity_data_class::getList(array(
            'select' => array('*'),
            'filter' => array('UF_XML_ID'=>$arrayFromObj[0])
        ))->fetch();

        if($arData)
            return $arData['ID'];
    }
}//end Class





//пример xml 
/*
<?xml version="1.0" encoding="UTF-8"?>
<КоммерческаяИнформация xmlns="urn:1C.ru:commerceml_2" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ВерсияСхемы="2.09" ДатаФормирования="2016-09-16T16:22:58" Ид="1">
	<ПользовательскиеСправочники СодержитТолькоИзменения="true">
		<Справочник>
			<Ид>Бренды</Ид>
			<Наименование>Бренды</Наименование>
			<Реквизиты>
				<Реквизит>
					<Ид>Наименование</Ид>
					<Наименование>Наименование</Наименование>
					<ТипЗначений>Строка</ТипЗначений>
				</Реквизит>
				<Реквизит>
					<Ид>ФайлБренда1</Ид>
					<Наименование>Файл бренда1</Наименование>
					<ТипЗначений>Строка</ТипЗначений>
				</Реквизит>
			</Реквизиты>
			<ЭлементыСправочника>
				<ЭлементСправочника>
					<Ид>baaf663b-c5a0-11e5-943e-14dae9ef6785</Ид>
					<НомерВерсии>AAAAAABOcvg=</НомерВерсии>
					<ЗначенияРеквизитов>
						<ЗначениеРеквизита>
							<Наименование>Наименование</Наименование>
							<Значение>JOYETECH</Значение>
						</ЗначениеРеквизита>
						<ЗначениеРеквизита>
							<Наименование>ФайлБренда1</Наименование>
							<Значение>import_files/ba/baaf663b-c5a0-11e5-943e-14dae9ef6785_99992f01-b7d3-4e3e-9014-90b2149048ae.png</Значение>
						</ЗначениеРеквизита>
					</ЗначенияРеквизитов>
				</ЭлементСправочника>
			</ЭлементыСправочника>
		</Справочник>
	</ПользовательскиеСправочники>
</КоммерческаяИнформация>
*/


