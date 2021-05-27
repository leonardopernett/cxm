
angular.module("experience_learning")

.controller("reportes_administracion_controller",function($scope,$http,$interval,$timeout,$rootScope){

  // NAVBAR NAME MODULE
  $rootScope.module.name = "Administrar reportes";
  $rootScope.module.permit = "88";

  // LISTA DE AREAS DE TRABAJO
  $scope.list_workspaces = [];

  // LISTA DE REPORTES POR AREA DE TRABAJO
  $scope.list_reports = [];

  $scope.current_workspace_id = "";

  // BUSCAR AREAS DE TRABAJO
  $scope.get_list_workspaces = function(){
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/get_list_workspaces"
    }).then(function(response){
      $rootScope.preloader = false;
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al buscar las areas de trabajo","error");
        return;
      }
      if(response.data.status == "1"){
        $scope.current_workspace = "";
        $scope.list_workspaces = response.data.data;
      }
      $rootScope.recharge_tooltips();
    },function(){
      $rootScope.fail_request("Error al buscar las areas de trabajo");
    });
    // AJAX END
  }
  // BUSCAR AREAS DE TRABAJO END


  // BUSCAR REPORTE ASOCIADO EN UN AREA DE TRABAJO
  $scope.search_reports_from_workspace = function(workspace){
    $scope.current_workspace = workspace;
    $scope.get_reports_by_workspace();
  }
  // BUSCAR REPORTE ASOCIADO EN UN AREA DE TRABAJO END

  // ABRIR SWAL ALERT PARA CREAR AREA DE TRABAJO
  $scope.open_swal_new_workspace = function(){
    swal({
      input: 'text',
      text: "Nombre del área de trabajo",
      inputPlaceholder: 'Escribe el nombre del area de trabajo',
      reverseButtons: false, 
      showConfirmButton: true, 
      showCancelButton: true, 
      showCloseButton: true,
      confirmButtonText : 'Crear', 
      cancelButtonText : 'Cancelar',
      inputValidator : function(value){
        if (!value) {
          return 'Ingresa el nombre del area de trabajo!'
        }
      }
    }).then(function(val){
      if(val.value != undefined){
        $scope.create_workspace(val.value);
      }
    });
  };
  // ABRIR SWAL ALERT PARA CREAR AREA DE TRABAJO

  // CREAR AREA DE TRABAJO
  $scope.create_workspace = function(workspace_name){
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/create_workspace",
      data : {
        workspace_name : workspace_name
      }
    }).then(function(response){
      $rootScope.preloader = false;
      console.log(response);
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al crear el area de trabajo","error");
        return;
      }
      if(response.data.status == "1"){
        swal("",response.data.data,"success").then(function(){
          $scope.get_list_workspaces();
        });
      }
    },function(){
      $rootScope.fail_request("Error al crear el area de trabajo");
    });
    // AJAX END
  };
  // CREAR AREA DE TRABAJO END

  // BUSCAR REPORTES DE UN AREA DE TRABAJO
  $scope.get_reports_by_workspace = function(){
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/get_reports_by_workspace",
      data : {
        workspace_id : $scope.current_workspace.id
      }
    }).then(function(response){
      $rootScope.preloader = false;
      $rootScope.recharge_tooltips();
      console.log(response);
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al buscar los reportes de este workspace","error");
        return;
      }
      if(response.data.status == "1"){
        $scope.list_reports = response.data.data;
        if(response.data.data.length == 0){
          swal("","No se encontraron reportes en esta area de trabajo","info");
        }
      }
    },function(){
      $rootScope.fail_request("Error al buscar los reportes de este workspace");
    });
    // AJAX END
  };
  // BUSCAR REPORTES DE UN AREA DE TRABAJO END

  // BUSCAR Y VISUALIZAR REPORTE
  $scope.search_report = function(report_id,current_report){
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/search_report",
      data : {
        workspace_id : $scope.current_workspace.id,
        report_id : report_id
      }
    }).then(function(response){
      $rootScope.preloader = false;
      console.log("primero",response);
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al buscar el reporte","error");
        return;
      }
      if(response.data.status == "1"){
        console.log("entro");
        $scope.name_report = current_report.name;
        $("#modal_show_report").modal("show");
        var models = window['powerbi-client'].models;
  
        var embedConfiguration = {
          type: 'report',
          id: report_id,
          embedUrl: 'https://app.powerbi.com/reportEmbed',
          tokenType: models.TokenType.Embed,
          accessToken: response.data.data
        };
  
        var $reportContainer = $('#container_report');
        var report = powerbi.embed($reportContainer.get(0), embedConfiguration);

        $scope.selectedPluginReport = report;
      }
    },function(){
      $rootScope.fail_request("Error al buscar el reporte");   
    });
    // AJAX END
  };
  // BUSCAR Y VISUALIZAR REPORTE END

  // VER REPORTE FULL SCREEN
  $scope.show_report_full_screen = function(){
    if($scope.selectedPluginReport != undefined){
      $scope.selectedPluginReport.fullscreen();
    }
  };
  // VER REPORTE FULL SCREEN END

  // BUSCAR USUARIOS PARA ASIGNAR PERMISOS EN UN REPORTE
  $scope.search_user_permits = function(reporte){
    $scope.current_report = reporte;
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/search_user_permits",
      data : {
        reporte : reporte
      }
    }).then(function(response){
      $rootScope.preloader = false;
      console.log(response);
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al buscar los usuarios","error");
        return;
      }
      if(response.data.status == "1"){
        $scope.name_report = reporte.name;
        $scope.list_users_permits = response.data.data;
        $("#modal_assign_permits").modal("show");
      }
    },function(){
      $rootScope.fail_request();
    });
    // AJAX END
  };
  // BUSCAR USUARIOS PARA ASIGNAR PERMISOS EN UN REPORTE END

  // GUARDAR PERMISOS DE REPORTE - USUARIO
  $scope.save_report_user_permits = function(){

    var list_users_with_permit = [];
    
    for(var i = 0; i < $scope.list_users_permits.length; i++){
      if($scope.list_users_permits[i]["tiene_permiso"] == "1" ){
        list_users_with_permit.push({
          id_usuario : $scope.list_users_permits[i]["id"],
          id_reporte : $scope.current_report.id,
          id_workspace : $scope.current_workspace.id
        });
      }
    }

    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/save_report_user_permits",
      data : {
        list_users : JSON.stringify(list_users_with_permit),
        reporte : $scope.current_report,
        workspace : $scope.current_workspace
      }
    }).then(function(response){
      $rootScope.preloader = false;
      console.log(response);

      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al guardar los permisos","error");
        return;
      }
      if(response.data.status == "1"){
        swal("",response.data.data,"success").then(function(){
          $("#modal_assign_permits").modal("hide");
        });
      }
    },function(){
      $rootScope.fail_request("Error al guardar los permisos");
    });
    // AJAX END
    console.log(list_users_with_permit);

  };
  // GUARDAR PERMISOS DE REPORTE - USUARIO END

  // FUNCTION DUPLICATE OR DELETE REPORT
  $scope.alter_report = function(tipo,reporte,new_name_report){
    // tipo 1: eliminar
    // tipo 2: duplicar
  
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/alter_report",
      data : {
        tipo : tipo,
        reporte : reporte,
        workspace : $scope.current_workspace,
        new_name_report : new_name_report
      }
    }).then(function(response){
      $rootScope.preloader = false;
      console.log(response);
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al ejecutar esta accion","error");
        return;
      }
      if(response.data.status == "1"){
        swal("",response.data.data,"success").then(function(){
          $scope.get_reports_by_workspace();
        });
      }
    },function(){
      $rootScope.fail_request("Error al ejecutar esta accion");
    });
    // AJAX END

  };
  // FUNCTION DUPLICATE OR DELETE REPORT END

  // OPEN SWAL NUEVO NOMBRE DEL REPORTE DUPLICADO
  $scope.open_swal_duplicate_report = function(reporte){
    swal({
      input: 'text',
      text: "Nombre del nuevo reporte",
      inputPlaceholder: 'Escribe el nombre que tendra el nuevo reporte',
      reverseButtons: false, 
      showConfirmButton: true, 
      showCancelButton: true, 
      showCloseButton: true,
      confirmButtonText : 'Duplicar', 
      cancelButtonText : 'Cancelar',
      inputValidator : function(value){
        if (!value) {
          return 'Ingresa el nombre del nuevo reporte!'
        }
      }
    }).then(function(val){
      if(val.value != undefined){
        $scope.alter_report(2,reporte,val.value);
      }
    });
  };
  // OPEN SWAL NUEVO NOMBRE DEL REPORTE DUPLICADO END

  // OPEN SWAL CONFIRMAR ELIMINACION DEL REPORTE
  $scope.swal_confirm_delete_report = function(reporte){
    // SWAL
    swal($rootScope.text_swal_delete_confirm).then(function(result) {
  		if (result.value) {
        $scope.alter_report(1,reporte);
  		}
  	})
    // SWAL END
  };
  // OPEN SWAL CONFIRMAR ELIMINACION DEL REPORTE


  // OPEN SWAL CONFIRMAR ELIMINACION DEL REPORTE
  $scope.swal_confirm_delete_workspace = function(workspace){
    console.log(workspace);
    swal($rootScope.text_swal_delete_confirm).then(function(result) {
  		if (result.value) {
        $scope.delete_workspace(workspace);
  		}
  	})
  };
  // OPEN SWAL CONFIRMAR ELIMINACION DEL REPORTE

  // FUNCTION DELETE WORKSPACE
  $scope.delete_workspace = function(workspace){
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/delete_workspace",
      data : {
        workspace : workspace
      }
    }).then(function(response){
      $rootScope.preloader = false;
      console.log(response);
      if(response.data.status == undefined || response.data.status == null || response.data.status == "0"){
        swal("","Error al eliminar este workspace","error");
        return;
      }
      if(response.data.status == "1"){
        swal("",response.data.data,"success").then(function(){
          $scope.list_reports = [];
          $scope.get_list_workspaces();     
        });
      }
    },function(){
      $rootScope.fail_request("Error al eliminar este workspace");
    });
    // AJAX END
  };
  // FUNCTION DELETE WORKSPACE END

  
  


  // AUTOCALL FUNCTIONS
  $( document ).ready(function() {
    $scope.current_id_workspace
    $scope.get_list_workspaces();
  });
  // AUTOCALL FUNCTIONS END

});
