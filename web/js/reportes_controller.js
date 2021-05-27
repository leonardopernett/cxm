
angular.module("experience_learning")

.controller("reportes_controller",function($scope,$http,$interval,$timeout,$rootScope){

  // NAVBAR NAME MODULE
  $rootScope.module.name = "Reportes";
  $rootScope.module.permit = "87";

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
        $scope.current_workspace_id = "";
        $scope.list_workspaces = response.data.data;
      }
      console.log(response);
    },function(){
      $rootScope.fail_request("Error al buscar las areas de trabajo");
    });
    // AJAX END
  }
  // BUSCAR AREAS DE TRABAJO END


  // BUSCAR REPORTE ASOCIADO EN UN AREA DE TRABAJO
  $scope.search_reports_from_workspace = function(id_workspace){
    $scope.current_workspace_id = id_workspace;
    $scope.get_reports_by_workspace();
    console.log(id_workspace);
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


  // BUSCAR REPORTES DE UN AREA DE TRABAJO
  $scope.get_reports_by_workspace = function(){
    // AJAX
    $rootScope.preloader = true;
    $http({
      method: "post",
      url: base_url + "reportes/reportes_administracion/get_reports_by_workspace",
      data : {
        workspace_id : $scope.current_workspace_id
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
        workspace_id : $scope.current_workspace_id,
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

  // AUTOCALL FUNCTIONS
  $( document ).ready(function() {
    $scope.current_id_workspace
    $scope.get_list_workspaces();
  });
  // AUTOCALL FUNCTIONS END

});
