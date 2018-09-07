<?php
class settingAd extends baseObject{
  var $Prefix = "settingAd";
  var $formName = "settingAdForm";
  var $tableName = "ad_setting";
  var $username = "";
  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){

      case 'daftarRender':{
        if(empty($jumlahData))$jumlahData = "25";
        if(empty($pageKe))$pageKe = "1";
        if(!empty($jumlahData)){
          if($pageKe == 1){
             $queryLimit  = " limit 0,$jumlahData";
          }else{
             $dataMulai = ($pageKe - 1)  * $jumlahData;
             $queryLimit  = " limit $dataMulai,$jumlahData";
          }
        }
        $kondisi = $this->generateCondition();
        $cek = "select * from $this->tableName $kondisi "." $queryLimit";
        $content=array(
          'tableContent' => $this->generateTable($kondisi." $queryLimit"),
          'tableFooter' => $this->tableFooter("select * from $this->tableName $kondisi"),
        );
  		break;
  		}
      case 'filterRender':{
        $content = $this->filterBar();
  		break;
  		}
      case 'Baru':{
        $content = $this->Baru();
  		break;
  		}
      case 'Edit':{
        $content = $this->Edit($settingAd_cb[0]);
  		break;
  		}
      case 'saveEdit':{

        if(empty($err)){
          $dataInsert = array(
            'ad_unit' => $adUnit,
            'delay' => $delayAd,
            'point' => $pointAd,
            'error_message' => $errorMessage,
            'status' => $statusAd,
          );
          $query = $this->sqlUpdate($this->tableName,$dataInsert,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($settingAd_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$settingAd_cb[$i]."'";
          $this->sqlQuery($query);
        }
        $cek = $query;
        break;
      }
      default:{
        $content = "API NOT FOUND";
      break;
      }
	 }

    return json_encode(array ('cek'=>$cek, 'err'=>$err, 'content'=>$content));
  }

  function __construct(){
    if(!isset($_GET['API'])){
        if(empty($_GET['action'])){
          echo $this->pageShow();
        }else{
          if($_GET['action'] == 'new'){
            echo $this->pageShowNew();
          }else{
            echo $this->pageShowEdit($_GET['idEdit']);
          }
        }
    }else{
       echo $this->jsonGenerator();
    }
  }
  function loadScript(){
    return "
    <script type='text/javascript' src='js/settingAd/settingAd.js'></script>
    <script type='text/javascript' src='assets/widgets/datepicker/datepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/multi-select/multiselect.js'></script>
    <script type='text/javascript'>
      $( document ).ready(function() {
        $this->Prefix.loading();
      });
    </script>
    ";
  }

  function setMenuEdit(){
    $setMenuEdit = "
    <div id='header-nav-right'>
    <a href='#' class='hdr-btn popover-button' title='Search' data-placement='bottom' data-id='#popover-search'>
      <i class='glyph-icon icon-search'></i>
    </a>
    <span id='filterRenderColumn'></span>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Edit(); title='Edit'>
      <i class='glyph-icon icon-pencil'></i>
    </a>


    </div>

    ";
    return $setMenuEdit;
  }
  function filterBar(){
    foreach ($_REQUEST as $key => $value) {
        $$key = $value;
    }
    if(empty($jumlahData))$jumlahData = "25";
    if(empty($pageKe))$pageKe = "1";
    $content = "
      <div class='hide' id='popover-search' >
        <div class='pad5A '>
            <div class='form-group'>
              <div class='row' style='margin-top:5px !important;'>
                <label class='col-sm-1 control-label' style='margin-top:6px;'>Data / Halaman</label>
                <div class='col-sm-1'>
                  ".$this->numberText(array(
                    "id" => 'jumlahData',
                    "value" => $jumlahData,
                    "params" => "onkeypress=$this->Prefix.setValueFilter(this)"
                  ))."
                </div>
              </div>
              <div class='row' style='margin-top:5px !important;'>
                <label class='col-sm-1 control-label' style='margin-top:6px;'></label>
                <div class='col-sm-1'>
                  <input type='hidden' id='pageKe' name='pageKe' value='$pageKe'>
                  <input type='hidden' id='limitTable' name='limitTable' value='$jumlahData'>
                  <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
                      <a class='btn btn-primary' >Tampilkan</a>
                  </span>
                </div>
              </div>

          </div>
        </div>
      </div>
    ";
    return $content;
  }
  function pageContent(){
    $pageContent = "
    <div id='page-title'>
      <h2>SETTING</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
            <form name='$this->formName' id='$this->formName'>
              <span id='daftarRenderColumn'>
                ".$this->generateTable("")."
              </span>
            </form>
            <span id='tableFooter' ></span>
          </div>
      </div>
      <div id='tempatModal'> </div>
    </div>

    ";
    return $pageContent;
  }

  function setKolomHeader(){
    $kolomHeader = "
    <thead>
      <tr>
          <th style='text-align:center;width:1%;'>No</th>
          <th style='text-align:center;width:1%;'>".$this->checkAll(25,$this->Prefix)."</th>
          <th style='text-align:center;width:10%;'>Nama </th>
          <th style='text-align:center;width:20%;'>Ad Unit</th>
          <th style='text-align:center;width:20%;'>Error Message</th>
          <th style='text-align:center;width:10%;'>Delay</th>
          <th style='text-align:center;width:10%;'>Point</th>
          <th style='text-align:center;width:5%;'>Status</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }

    $tableRow = "
    <tr class='$classRow'>
        <td style='text-align:center;'>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td>$name</td>
        <td>$ad_unit</td>
        <td>$error_message</td>
        <td style='text-align:right;'>".$this->numberFormat($delay)."</td>
        <td style='text-align:right;'>".$this->numberFormat($point)."</td>
        <td style='text-align:center;'>".$status."</td>
    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    foreach ($_REQUEST as $key => $value) {
        $$key = $value;
    }
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ad_setting $kondisiTable");
    while ($dataServer = $this->sqlArray($getDataServer)) {
      $kolomData.= $this->setKolomData($no,$dataServer);
      $no++;
    }

    $htmlTable = "
        <table class='table table-bordered table-striped table-condensed table-hover'  role='grid' aria-describedby='dataServer_info' style='width: 100%;font-size:12px;' id='dataServer' >
            ".$this->setKolomHeader()."
            <tbody>
              $kolomData
            </tbody>
        </table>

        <input type='hidden' name='".$this->Prefix."_jmlcek' id='".$this->Prefix."_jmlcek' value='0'>

    ";
    return $htmlTable;
  }

  function pageShowNew(){
    $pageShow = "
    ".$this->loadJSandCSS()."
        <body class='fixed-header'>
        <div id='loading'>
            <div class='spinner'>
                <div class='bounce1'></div>
                <div class='bounce2'></div>
                <div class='bounce3'></div>
            </div>
        </div>
        <div id='page-wrapper'>
        ".$this->emptyMenuBar()."
        ".$this->sidebar()."
        <div id='page-content-wrapper'>
            <div id='page-content'>
              <div class='container'>
                ".$this->formBaru()."
              </div>
            </div>
        </div>
      </div>
      </body>
      </html>
          ";

    return $pageShow;
  }
  function pageShowEdit($idEdit){
    $pageShow = "
    ".$this->loadJSandCSS()."
        <body class='fixed-header'>
        <div id='loading'>
            <div class='spinner'>
                <div class='bounce1'></div>
                <div class='bounce2'></div>
                <div class='bounce3'></div>
            </div>
        </div>
        <div id='page-wrapper'>
        ".$this->emptyMenuBar()."
        ".$this->sidebar()."
        <div id='page-content-wrapper'>
            <div id='page-content'>
              <div class='container'>
                ".$this->formEdit($idEdit)."
              </div>
            </div>
        </div>
      </div>
      </body>
      </html>
          ";

    return $pageShow;
  }

  function Edit($idEdit){
    $formCaption = "Edit";
    $getDataEdit = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id = '$idEdit'"));
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalForm' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
      <form name='".$this->formName."_input' id='".$this->formName."_input'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$formCaption</h4>
                </div>
                <div class='modal-body'>

                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Name</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "value" => $getDataEdit['name'],
                          "params" => "readonly"
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Ad Unit</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'adUnit',
                          "value" => $getDataEdit['ad_unit']
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Error Message</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'errorMessage',
                          "value" => $getDataEdit['error_message']
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Delay</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'delayAd',
                          "value" => $getDataEdit['delay']
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Point</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'pointAd',
                          "value" => $getDataEdit['point']
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Status</label>
                      <div class='col-sm-9'>
                        ".$this->cmbArray("statusAd",$getDataEdit['status'],array(array("ACTIVE","ACTIVE"),array("BLOCK","BLOCK")),"-- STATUS --","class='form-control'")."
                      </div>
                    </div>
                  </div>
                </div>
                <div class='modal-footer'>
                    <input type='hidden' name='idEdit' id='idEdit' value='$idEdit'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveEdit();>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
      </form>
    </div>";

    return $content;
  }

  function dateConversion($tanggal){
    // $arrayTanggal = explode("/",$tanggal);
    // $getJam = explode("-",$tanggal);
    // $arrayJam = explode(":",$getJam[1]);
    // return "20".str_replace($arrayJam[0].":".$arrayJam[1].":".$arrayJam[2],"",$arrayTanggal[2])."".$this->genNumber($arrayTanggal[1])."-".$this->genNumber($arrayTanggal[0])." ".$arrayJam[0].":".$arrayJam[1];
    $arrayTanggal =  explode("+",$tanggal);
    $explodeJam = explode(":",$arrayTanggal[1]);
    return $arrayTanggal[0]." ".$explodeJam[0].":".$explodeJam[1];
  }
  function dateToNumber($tanggal){
    $tanggal = str_replace("-","",$tanggal);
    $tanggal = str_replace(" ","",$tanggal);
    $tanggal = str_replace(":","",$tanggal);
    return $tanggal;
  }
  function genNumber($num, $dig=2){
    $tambah = pow(10,$dig);//100000;
    $tmp = ($num + $tambah).'';
    return substr($tmp,1,$dig);
  }
  function generateMonth($arrayMonth){
    return $arrayMonth['month'];
  }
  function generateCondition(){
    foreach ($_REQUEST as $key => $value) {
       $$key = $value;
    }
    $arrKondisi = array();
    if(!empty($filterNama))$arrKondisi[] = " nama like '%$filterNama%'";
    if(!empty($filterEmail))$arrKondisi[] = " email like '%$filterNama%'";
    if(!empty($filterNomorTelepon))$arrKondisi[] = " nomor_telepon '%$filterNomorTelepon%'";
    $Kondisi= join(' and ',$arrKondisi);
    $arrOrders = array();
		$arrOrders[] = " id " ;
    if(sizeof($arrOrders) != 0){
      $Order= " order by ".implode(',',$arrOrders);
    }
		$Kondisi = $Kondisi =='' ? '':' Where '.$Kondisi;
    return $Kondisi.$Order;
  }

  function tableFooter($query){
    foreach ($_REQUEST as $key => $value) {
       $$key = $value;
    }
    $rowCount = $this->sqlRowCount($this->sqlQuery($query));
    if(empty($jumlahData))$jumlahData = 25;
    if(empty($pageKe))$pageKe = 1;
    $jumlahPage =ceil($rowCount / $jumlahData) ;
    for ($i=1; $i <= $jumlahPage ; $i++) {
        if($pageKe == $i){
          $dataPagging .= "<li class='active liPagging'>
                              <a onclick=$this->Prefix.currentPage($i)>$i</a>
                          </li>";
        }else{
          $dataPagging .= "<li class='liPagging'>
                              <a onclick=$this->Prefix.currentPage($i)>$i</a>
                          </li>";
        }

    }
    $content = "
      <form name='formHiddenFilter' id='formHiddenFilter' style='display:none;'>
        <span id='filterHidden' style='display:none;'>
        </span>
      </form>

      <ul class='pagination pagination-info'>
        $dataPagging
      </ul>";
    return $content;
  }

}
$settingAd = new settingAd();


 ?>
