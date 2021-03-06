var refTradePoint = new baseObject2({
	prefix : 'refTradePoint',
	url : 'pages.php?page=refTradePoint',
	formName : 'refTradePointForm',
	idFileCheck : 0,
	optionPushFile : 0,
	optionPushDataBase : 0,
	getJumlahChecked: function() {
    var jmldata= document.getElementById( this.prefix+'_jmlcek' ).value;
    for(var i=0; i < jmldata; i++){
      var box = document.getElementById( this.prefix+'_cb' + i);
      if( box.checked){
        break;
      }
    }
    var err = "";
    if(jmldata == 0){
        err = "Pilih data";
    }else if(jmldata > 1){
        err = "Pilih hanya satu data";
    }
    return err;
	},
	checkSemua: function(jumlahData,fldName,elHeaderChecked,elJmlCek,fuckYeah) {
    if (!fldName) {
      fldName = 'cb';
    }
    if (!elHeaderChecked) {
      elHeaderChecked = 'toggle';
    }
    var c = fuckYeah.checked;
    var n2 = 0;
    for (i=0; i < jumlahData ; i++) {
     cb = document.getElementById(fldName+i);
     if (cb) {
       cb.checked = c;
       n2++;
     }
    }
    if (c) {
     document.getElementById(elJmlCek).value = n2;
    } else {
     document.getElementById(elJmlCek).value = 0;
    }
	},
	thisChecked: function(idCheckbox,elJmlCek) {
    var c = document.getElementById(idCheckbox).checked;
    var jumlahCheck = parseInt($("#"+elJmlCek).val());
    if(c){
        document.getElementById(elJmlCek).value = jumlahCheck + 1;
    }else{
        document.getElementById(elJmlCek).value = jumlahCheck - 1;
    }
	},
	formatCurrency: function(num) {
		num = num.toString().replace(/\$|\,/g,'');
		if(isNaN(num))
		num = "0";
		sign = (num == (num = Math.abs(num)));
		num = Math.floor(num*100+0.50000000001);
		cents = num%100;
		num = Math.floor(num/100).toString();
		if(cents<10)
		cents = "0" + cents;
		for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+'.'+
		num.substring(num.length-(4*i+3));
		return (((sign)?'':'-') + '' + num + ',' + cents);
	},
	setValueFilter: function(a){
		$("#"+a.id).val(a.value);
		// this.refreshList();
	},
	loading: function(){
			this.filterRender();
			this.daftarRender(1,25);
	},
	Baru: function(){
		$.ajax({
			type:'POST',
			data : $("#"+this.formName).serialize(),
			url: this.url+'&API=Baru',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#modalForm").remove();
					$("#tempatModal").html(resp.content);
					$("#modalForm").modal();
					"use strict";
					$(".multi-select").multiSelect();
					$(".ms-container").append('<i class="glyph-icon icon-exchange"></i>');
				}else{
					refTradePoint.errorAlert(resp.err);
				}
			}
		});

	},
	Edit: function(){
		var errMsg = refTradePoint.getJumlahChecked();
		urlEdit = this.url;
		if(errMsg == ''){
			$.ajax({
				type:'POST',
				data : $("#"+this.formName).serialize(),
				url: this.url+'&API=Edit',
				success: function(data) {
					var resp = eval('(' + data + ')');
					if(resp.err==''){
						$("#modalForm").remove();
						$("#tempatModal").html(resp.content);
						$("#modalForm").modal();
						"use strict";
						$(".multi-select").multiSelect();
						$(".ms-container").append('<i class="glyph-icon icon-exchange"></i>');
					}else{
						refTradePoint.errorAlert(resp.err);
					}
				}
			});
		}else{
			 refTradePoint.errorAlert(errMsg);
		}
	},
	Hapus: function(){
  var errMsg = this.getJumlahChecked();
  if(errMsg == '' || errMsg=='Pilih hanya satu data'){
    swal({
          title: "Hapus Data ?",
          text: "",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Ya",
          cancelButtonText: "Tidak",
          closeOnConfirm: false
        },
        function(){
          $.ajax({
              type:'POST',
							data : $("#"+refTradePoint.formName).serialize(),
				      url: refTradePoint.url+'&API=Hapus',
              success: function(data) {
                var resp = eval('(' + data + ')');
                if(resp.err==''){
                  refTradePoint.suksesAlert("Data Terhapus",refTradePoint.daftarRender);
                }else{
                  refTradePoint.errorAlert(resp.err);
                }
              }
            });
        });
    }else{
      refTradePoint.errorAlert(errMsg);
    }
  },
	homePage: function(){
		window.location = refTradePoint.url;
	},

	saveNew: function(){
	  var me = this;
		this.showLoading();
		$.ajax({
			type:'POST',
			data : $("#"+refTradePoint.formName+"_input").serialize(),
			url: refTradePoint.url+'&API=saveNew',
				success: function(data) {
					me.closeLoading();
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							me.closeModal();
							swal("Success", "Data Tersimpan", "success");
							me.daftarRender();
						}else{
							refTradePoint.errorAlert(resp.err);
						}
					}
		});
	},
	saveEdit: function(){
	  var me = this;
		this.showLoading();
		$.ajax({
			type:'POST',
			data : $("#"+refTradePoint.formName+"_input").serialize(),
			url: refTradePoint.url+'&API=saveEdit',
				success: function(data) {
					me.closeLoading();
					var resp = eval('(' + data + ')');
						if(resp.err==''){
							me.closeModal();
							swal("Success", "Data Tersimpan", "success");
							me.daftarRender();
						}else{
							refTradePoint.errorAlert(resp.err);
						}
					}
		});
	},
	filterRender: function(){
		$.ajax({
			type:'POST',
			data : $("#formFilter").serialize(),
			url: refTradePoint.url+'&API=filterRender',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#filterRenderColumn").html(resp.content);
				}else{
					refTradePoint.errorAlert(resp.err);
				}
			}
		});
	},
	daftarRender: function(){
		refTradePoint.showLoading();
		$.ajax({
			type:'POST',
			data : $("#formFilter").serialize(),
			url: refTradePoint.url+'&API=daftarRender',
			success: function(data) {
				refTradePoint.closeLoading();
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#daftarRenderColumn").html(resp.content.tableContent);
					$("#tableFooter").html(resp.content.tableFooter);
				}else{
					refTradePoint.errorAlert(resp.err);
				}
			}
		});
	},

	currentPage: function(pageKE){
		$("#pageKe").val(pageKE);
		$("#filterHidden").html($("#popover-search").html());
		$.ajax({
			type:'POST',
			data : $("#formHiddenFilter").serialize(),
			url: refTradePoint.url+'&API=daftarRender',
			success: function(data) {
				var resp = eval('(' + data + ')');
				if(resp.err==''){
					$("#daftarRenderColumn").html(resp.content.tableContent);
					$("#tableFooter").html(resp.content.tableFooter);
				}else{
					refTradePoint.errorAlert(resp.err);
				}
			}
		});
	},
	refreshList: function(){
		$(".popover-button").click();
		if($("#pageKe").val() != "1"){
			$("#pageKe").val(1);
		}
		this.filterRender();
		this.daftarRender()
	},
  imageChanged: function() {
    var me = this;
    var filesSelected = document.getElementById("fileInputTradePoint").files;
    if (filesSelected.length > 0) {
      var fileToLoad = filesSelected[0];
      var fileReader = new FileReader();
      fileReader.onload = function(fileLoadedEvent) {
        $("#thumbnailImages").attr("src", fileLoadedEvent.target.result);
        $("#gambarTradePoint").val(fileLoadedEvent.target.result);
      };
      fileReader.readAsDataURL(fileToLoad);
    }
  }






});
