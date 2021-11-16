<?php

namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\db\mssql\PDO;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use app\models\UploadForm2;
use GuzzleHttp;
use app\models\HvInfopersonal;
use app\models\Hobbies;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_DefaultReadFilter;
use PHPExcel_Shared_Date;



class HvInfopersonalController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','excelexport'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                              return Yii::$app->user->identity->isAdminSistema();
                          },
                ],
              ]
            ],
          'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
              'delete' => ['get'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
        ],
        ];
    } 

    public function beforeAction($action){
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action);
    }
   
    public function actionIndex(){


  

      $profesion = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico c WHERE  c.idhvacademico = 4')->queryAll();
      $especializacion = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico c WHERE  c.idhvacademico = 2')->queryAll();
      $maestria = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico c WHERE  c.idhvacademico = 3')->queryAll();
      $doctorado = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico c WHERE  c.idhvacademico = 1')->queryAll();



        $sessiones = Yii::$app->user->identity->id;

        $rol =  new Query;
        $rol     ->select(['tbl_roles.role_id'])
                    ->from('tbl_roles')
                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                                'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                    ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
        $command = $rol->createCommand();
        $roles = $command->queryScalar();
      

      if( (int)$roles==301 || (int)$roles==299 || (int)$roles==270 || (int)$roles==305 || (int)$roles==309 || (int)$roles==304 ) {
          $modelos = new HvInfopersonal();
          $unicos = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal u  WHERE u.usua_id=:id')
          ->bindParam(':id', Yii::$app->user->identity->id)
          ->queryAll();
        
          $model = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal')->queryAll();
          return $this->render('index',[
            'model'             => $model,
            'unicos'            => $unicos,
            'modelos'           => $modelos,
            'roles'             => $roles,
            'profesion'         => $profesion,
            'especializacion'   => $especializacion,
            'maestria'          => $maestria,
            'doctorado'         => $doctorado
          
          
        ]);
      }else{
        Yii::$app->session->setFlash('error','Acceso denegado');
          return $this->redirect(['/']);
      }

    }

    public function actionCrear(){

        $pcrc = Yii::$app->get('dbjarvis')->createCommand('select * from dp_pcrc')->queryAll();
        $clientes = Yii::$app->get('dbjarvis')->createCommand('select * from dp_clientes')->queryAll();
        $posicion = Yii::$app->get('dbjarvis')->createCommand('select * from dp_posicion')->queryAll();
        $paises = Yii::$app->db->createCommand('SELECT pais FROM tbl_hv_pais ORDER BY pais ASC')->queryAll();
        $ciudad = Yii::$app->db->createCommand('SELECT ciudad  FROM tbl_hv_ciudad ORDER BY ciudad ASC')->queryAll();
        $director_programa = Yii::$app->get('dbjarvis')->createCommand('select director_programa from dp_centros_costos GROUP BY documento_director')
                                      ->queryAll();

        $gerente = Yii::$app->get('dbjarvis')->createCommand("select gerente_cuenta from dp_centros_costos WHERE gerente_cuenta !='0' AND gerente_cuenta !='Sin info' GROUP BY documento_gerente")
                                              ->queryAll();
          Yii::$app->session->setFlash('success','usuario registrado exitosamenete');

        $profesion = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 4')
                                  ->queryAll();

          $especializacion = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 2')
                                    ->queryAll();

          $maestria = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 3')
                                    ->queryAll();

          $doctorado = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 1')
                                  ->queryAll();
     
    
              return $this->render('crear',[
                'clientes'         => $clientes, 
                'pcrc'             => $pcrc, 
                'posicion'         => $posicion, 
                'director_programa'=> $director_programa,
                'gerente'          => $gerente,
                'profesion'        => $profesion,
                'especializacion'  => $especializacion,
                'maestria'         => $maestria,
                'doctorado'        => $doctorado,
                'paises'        => $paises,
                'ciudad'        => $ciudad,
              ]);
    }

    public function actionStore(){
          $DATA = [
              'cliente' =>Yii::$app->request->post('client'),
              'director'=>implode(",",Yii::$app->request->post('director')),
              'gerente' =>implode(",",Yii::$app->request->post('gerente')),
              'pcrc'    =>implode(",",Yii::$app->request->post('pcrc')),
              'hvnombre' =>Yii::$app->request->post('hvnombre'),
              'hvidentificacion' =>Yii::$app->request->post('hvidentificacion'),
              'hvdireccionoficina' =>Yii::$app->request->post('hvdireccionoficina'),
              'hvdireccioncasa' =>Yii::$app->request->post('hvdireccioncasa'),
              'hvemailcorporativo' =>Yii::$app->request->post('hvemailcorporativo'),
              'hvmovil' =>Yii::$app->request->post('hvmovil'),
              'hvcontactooficina' =>Yii::$app->request->post('hvcontactooficina'),
              'hvpais' =>Yii::$app->request->post('hvpais'),
              'hvciudad' =>Yii::$app->request->post('hvciudad'),
              'hvmodalidatrabajo' =>Yii::$app->request->post('hvmodalidatrabajo'),
              'hvautorizacion' =>Yii::$app->request->post('hvautorizacion'),
              'hvsusceptible' =>Yii::$app->request->post('hvsusceptible'),
              'hvsatu' =>Yii::$app->request->post('hvsatu'),
              'areatrabajo' =>Yii::$app->request->post('areatrabajo'),
              'rol' =>Yii::$app->request->post('rol'),
              'antiguedadrol' =>Yii::$app->request->post('antiguedadrol'),
              'fechacontacto' =>Yii::$app->request->post('fechacontacto'),
              'tipo' =>Yii::$app->request->post('tipo'),
              'nivel' =>Yii::$app->request->post('nivel'),
              'afinidad' =>Yii::$app->request->post('afinidad'),
              'nombrejefe' =>Yii::$app->request->post('nombrejefe'),
              'cargojefe' =>Yii::$app->request->post('cargojefe'),
              'rolanterior' =>Yii::$app->request->post('rolanterior'),
              'profesion' =>Yii::$app->request->post('profesion'),
              'especializacion' =>Yii::$app->request->post('especializacion'),
              'maestria' =>Yii::$app->request->post('maestria'),
              'doctorado' =>Yii::$app->request->post('doctorado'),
              'estado'  => Yii::$app->request->post('estado'),
              'usua_id' =>Yii::$app->user->identity->id,
            ];


           $user = Yii::$app->db->createCommand('select * from tbl_hv_infopersonal where hvidentificacion=:identificacion')
                            ->bindParam(':identificacion', $DATA['hvidentificacion'] )
                            ->queryOne();
            
            if(isset($user['hvidentificacion'])){
              Yii::$app->session->setFlash('info3','El numero de documento ya se encuentra registrado'); 
              return $this->redirect(['index']); 
            }else{
              Yii::$app->db->createCommand()->insert('tbl_hv_infopersonal',$DATA)->execute();

               $message  = "<html><body>";
               $message .= "<h3>Se ha realizado Correctamente la creacion de un nuevo contacto</h3>";
               $message .= "<p><b>Contacto: </b>".Yii::$app->request->post('hvnombre') ."</p>";
               $message .= "<p><b>Identificacion: </b>".Yii::$app->request->post('hvidentificacion') ."</p>";
               $message .= "<p><b>Email Corporativo: </b>".Yii::$app->request->post('hvnombre') ."</p>";
               $message .= "</body></html>";
       
        
              Yii::$app->mailer->compose()
               ->setTo('engie.guerrero@grupokonecta.com')
               ->setFrom(Yii::$app->params['email_satu_from'])
               ->setSubject("Creaci n contacto Maestro Cliente CXM")
               // ->attach($tmpFile)
               ->setHtmlBody($message) 
               ->send(); 
               Yii::$app->session->setFlash('info2','Registro creado Exitosamente');   
               return $this->redirect(['crear']);
            }

        
      
    }
    
    public function actionUpdate($id){

        $DATA = [
        'cliente' =>Yii::$app->request->post('client'),
        'director'=>implode(",", Yii::$app->request->post('director')),
        'gerente' =>implode(",",Yii::$app->request->post('gerente')),
        'pcrc'    =>implode(",",Yii::$app->request->post('pcrc')),
        'hvnombre' =>Yii::$app->request->post('hvnombre'),
        'hvidentificacion' =>Yii::$app->request->post('hvidentificacion'),
        'hvdireccionoficina' =>Yii::$app->request->post('hvdireccionoficina'),
        'hvdireccioncasa' =>Yii::$app->request->post('hvdireccioncasa'),
        'hvemailcorporativo' =>Yii::$app->request->post('hvemailcorporativo'),
        'hvmovil' =>Yii::$app->request->post('hvmovil'),
        'hvcontactooficina' =>Yii::$app->request->post('hvcontactooficina'),
        'hvpais' =>Yii::$app->request->post('hvpais'),
        'hvciudad' =>Yii::$app->request->post('hvciudad'),
        'hvmodalidatrabajo' =>Yii::$app->request->post('hvmodalidatrabajo'),
        'hvautorizacion' =>Yii::$app->request->post('hvautorizacion'),
        'hvsusceptible' =>Yii::$app->request->post('hvsusceptible'),
        'hvsatu' =>Yii::$app->request->post('hvsatu'),
        'areatrabajo' =>Yii::$app->request->post('areatrabajo'),
        'rol' =>Yii::$app->request->post('rol'),
        'antiguedadrol' =>Yii::$app->request->post('antiguedadrol'),
        'fechacontacto' =>Yii::$app->request->post('fechacontacto'),
        'tipo' =>Yii::$app->request->post('tipo'),
        'nivel' =>Yii::$app->request->post('nivel'),
        'afinidad' =>Yii::$app->request->post('afinidad'),
        'nombrejefe' =>Yii::$app->request->post('nombrejefe'),
        'cargojefe' =>Yii::$app->request->post('cargojefe'),
        'rolanterior' =>Yii::$app->request->post('rolanterior'),
        'profesion' =>Yii::$app->request->post('profesion'),
        'especializacion' =>Yii::$app->request->post('especializacion'),
        'maestria' =>Yii::$app->request->post('maestria'),
        'doctorado' =>Yii::$app->request->post('doctorado'),
        'estado'  => Yii::$app->request->post('estado'),
        'usua_id' =>Yii::$app->user->identity->id,
      ];

        Yii::$app->db->createCommand()
        ->update('tbl_hv_infopersonal',$DATA,'idhvinforpersonal=:id')
        ->bindParam(':id',$id)
        ->execute();
        Yii::$app->session->setFlash('info','Registro Actualizado exitosamente'); 
        
        $message  = "<html><body>";
        $message .= "<h3>Se ha realizado Correctamente la actualizacion de un nuevo contacto</h3>";
        $message .= "<p><b>Contacto: </b>".Yii::$app->request->post('hvnombre') ."</p>";
        $message .= "<p><b>Identificacion: </b>".Yii::$app->request->post('hvidentificacion') ."</p>";
        $message .= "<p><b>Email Corporativo: </b>".Yii::$app->request->post('hvnombre') ."</p>";
        $message .= "</body></html>";


          Yii::$app->mailer->compose()
        ->setTo('engie.guerrero@grupokonecta.com')
        ->setFrom(Yii::$app->params['email_satu_from'])
        ->setSubject("Actualizaci n contacto Maestro Cliente CXM")
        // ->attach($tmpFile)
        ->setHtmlBody($message)
        ->send();   
        Yii::$app->session->setFlash('info2','Registro actualizado Exitosamente');   
        return $this->redirect(['detalle','id'=>$id]); 
      
    
   
    

    }

    public function actionDetalle($id){
      $pcrc = Yii::$app->get('dbjarvis')->createCommand('select * from dp_pcrc')->queryAll();
      $clientes = Yii::$app->get('dbjarvis')->createCommand('select * from dp_clientes')->queryAll();
      $posicion = Yii::$app->get('dbjarvis')->createCommand('select * from dp_posicion')->queryAll();
      $paises = Yii::$app->db->createCommand('SELECT pais FROM tbl_hv_pais ORDER BY pais ASC')->queryAll();
      $ciudad = Yii::$app->db->createCommand('SELECT ciudad  FROM tbl_hv_ciudad ORDER BY ciudad ASC')->queryAll();

      $director_programa = Yii::$app->get('dbjarvis')->createCommand('select director_programa from dp_centros_costos GROUP BY documento_director')
                                    ->queryAll();
      $gerente = Yii::$app->get('dbjarvis')->createCommand("select gerente_cuenta from dp_centros_costos WHERE gerente_cuenta !='0' GROUP BY documento_gerente")
                                           ->queryAll();




       Yii::$app->session->setFlash('success','usuario registrado exitosamenete');
 
      $profesion = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 4')
                                ->queryAll();
 
       $especializacion = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 2')
                                 ->queryAll();
 
       $maestria = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 3')
                                 ->queryAll();
 
       $doctorado = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_cursosacademico WHERE idhvacademico = 1')
                                ->queryAll();
 
       $data = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal where idhvinforpersonal=:id')->bindParam(':id',$id)->queryOne();
      
       
       return $this->render('editar',[
          'data'=>  $data,
          'clientes'         => $clientes, 
          'pcrc'             => $pcrc, 
          'posicion'         => $posicion, 
          'director_programa'=> $director_programa,
          'gerente'          => $gerente,
          'profesion'        => $profesion,
          'especializacion'  => $especializacion,
          'maestria'         => $maestria,
          'doctorado'        => $doctorado,
          'paises'        => $paises,
          'ciudad'        => $ciudad

       ]);
    }

    public function actionDelete($id){

        Yii::$app->db->createCommand('delete from tbl_hv_infopersonal where idhvinforpersonal = :id')
         ->bindParam(':id',$id)
         ->execute();
         Yii::$app->session->setFlash('delete','Usuario eliminado exitosamente');

         return $this->redirect(['index']); 

    }

    public function actionProfesion(){
       Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
           'idhvacademico'=> 4,
           'hv_cursos'=> Yii::$app->request->post('profesion')
       ])
       ->execute();
       Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
       return $this->redirect(['index']) ;
    }

    public function actionEspecializacion(){
      Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
        'idhvacademico'=> 2,
        'hv_cursos'=> Yii::$app->request->post('especializacion')
      ])
      ->execute();
      Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
      return $this->redirect(['index']) ;
    }

    public function actionMaestria(){
      Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
        'idhvacademico'=> 3,
        'hv_cursos'=> Yii::$app->request->post('maestria')
      ])
      ->execute();
      Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
      return $this->redirect(['index']) ;
    }

    public function actionDoctorado(){
      Yii::$app->db->createCommand()->insert('tbl_hv_cursosacademico',[
        'idhvacademico'=> 1,
        'hv_cursos'=> Yii::$app->request->post('doctorado')
     ])
      ->execute();
      Yii::$app->session->setFlash('list','Lista Agregada Exitosamente');
      return $this->redirect(['index']) ;
    }

    public function actionComplementaria($id){
          $model1 = new Hobbies();

          $dominancias = Yii::$app->db->createCommand('SELECT dominancia FROM tbl_hv_dominancias ORDER BY dominancia ASC ')->queryAll();

          $usuario = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal WHERE idhvinforpersonal=:id')
                            ->bindParam(':id', $id)
                            ->queryOne();

          $data = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal u WHERE u.idhvinforpersonal=:id')
                            ->bindParam(':id', $id)
                            ->queryOne();             

          $hijos = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_nombre_hijos WHERE user_id=:id')
                            ->bindParam(':id', $id)
                            ->queryOne();

          $hobbies = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_hobbies')->queryAll();
          $gustos = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_gustos')->queryAll();

          $dominanciapreselect = Yii::$app->db->createCommand('SELECT d.dominancia FROM  tbl_hv_dominancias d
          INNER JOIN tbl_hv_infopersonal i ON 
            d.dominancia = i.dominancia
          where i.idhvinforpersonal=:id
          GROUP BY d.dominancia')->bindParam(':id',$id)
          ->queryAll();

          $hobbieSelected = Yii::$app->db->createCommand('SELECT h.id , h.text, a.user_id FROM tbl_hv_info_hobbies a
          INNER JOIN tbl_hv_infopersonal u 
          ON u.idhvinforpersonal= a.user_id
          INNER JOIN tbl_hv_hobbies h
          ON h.id= a.hobbies_id
          where u.idhvinforpersonal=:id
          GROUP BY a.hobbies_id')->bindParam(':id',$id)
          ->queryAll();


          $gustosSelected = Yii::$app->db->createCommand('SELECT g.id , g.text, a.user_id FROM tbl_hv_info_gustos a
          INNER JOIN tbl_hv_infopersonal u 
          ON u.idhvinforpersonal= a.user_id
          INNER JOIN tbl_hv_gustos g
          ON g.id= a.gustos_id
          WHERE u.idhvinforpersonal=:id
          GROUP BY a.gustos_id
          ')->bindParam(':id',$id)->queryAll();
                      
          return $this->render('complementaria',[
                'usuario' => $usuario,
                'hobbies' => $hobbies,
                'gustos'  => $gustos,
                'hijos'   => $hijos,
                 'model1' => $model1,
                'hobbieSelected' =>   $hobbieSelected,
                'gustosSelected' =>   $gustosSelected,
                'data'=>$data,
                'dominancias' => $dominancias,
                'dominanciapreselect' => $dominanciapreselect
          ]);
    }           

    public function actionActualizar(){
            Yii::$app->db->createCommand()->update('tbl_hv_infopersonal',[
                  'estadocivil' => Yii::$app->request->post('estadocivil'),
                  'numerohijos' => Yii::$app->request->post('numerohijos'),
                  'dominancia'  => Yii::$app->request->post('dominancia'),
                  'estilosocial'=> Yii::$app->request->post('estilosocial'),
                  'hobbies' => implode(',',Yii::$app->request->post('hobbies')),
                  'gustos'  => implode(',',Yii::$app->request->post('gustos')),
            ],'idhvinforpersonal=:id')
            ->bindParam(':id',Yii::$app->request->post('id'))
            ->execute();

            $user_hijos = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_nombre_hijos WHERE user_id=:id')
            ->bindParam(':id',Yii::$app->request->post('id')) 
            ->queryOne();
    
            if(empty($user_hijos)){
              Yii::$app->db->createCommand()->insert('tbl_hv_nombre_hijos',[
                "nombre"  => Yii::$app->request->post('nombre'),
                "user_id" => Yii::$app->request->post('id')
              ])->execute();
            }else {
              Yii::$app->db->createCommand()->update('tbl_hv_nombre_hijos',[
                "nombre"  => Yii::$app->request->post('nombre'),
                "user_id" => Yii::$app->request->post('id')
              ],'user_id=:id') 
              ->bindParam(':id',Yii::$app->request->post('id')) 
              ->execute();
            }
              
            Yii::$app->session->setFlash('actualizar','Actualizacion complementaria realizada exitosamente');
            return $this->redirect(['complementaria','id'=>Yii::$app->request->post('id') ]); 
          
    }
  
    public function actionEventos($id){
     
          $usuarios = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal')->queryAll();
          $eventos = Yii::$app->db->createCommand("SELECT idhvinforpersonal,  hvnombre,cliente, director, gerente, pcrc, hvidentificacion,hvdireccionoficina,hvdireccioncasa,hvemailcorporativo,
          hvmovil,hvcontactooficina,nombre_evento, hvpais,hvciudad,hvmodalidatrabajo, areatrabajo,
          antiguedadrol,tipo, nivel, afinidad,nombrejefe, cargojefe, rolanterior, estadocivil,dominancia, numerohijos, estilosocial,
          id, tipo_evento, ciudad_evento, asistencia, fecha_evento FROM tbl_hv_eventos e
          INNER JOIN tbl_hv_infopersonal u
			 ON u.idhvinforpersonal=e.user_id
       
			  WHERE e.user_id=:id > 0 ORDER BY e.fecha_evento desc")
          ->bindParam(':id', $id)
          ->queryAll();


          $usuarioSelected =Yii::$app->db
              ->createCommand("SELECT idhvinforpersonal,  hvnombre from tbl_hv_infopersonal u WHERE u.idhvinforpersonal=:id")
              ->bindParam(':id',$id)
              ->queryOne();


          $eventoAgrupado = Yii::$app->db->createCommand("SELECT idhvinforpersonal,  hvnombre,cliente, director, gerente, pcrc, hvidentificacion,hvdireccionoficina,hvdireccioncasa,hvemailcorporativo,
          hvmovil,hvcontactooficina,nombre_evento, hvpais,hvciudad,hvmodalidatrabajo, areatrabajo,
          antiguedadrol,tipo, nivel, afinidad,nombrejefe, cargojefe, rolanterior, estadocivil,dominancia, numerohijos, estilosocial,
          id, tipo_evento, ciudad_evento, asistencia, fecha_evento, user_id FROM tbl_hv_eventos e
          INNER JOIN tbl_hv_infopersonal u
			 ON u.idhvinforpersonal=e.user_id
			WHERE e.user_id=28 > 0 GROUP BY hvnombre")
          ->bindParam(':id', $id)
          ->queryOne();

          return $this->render('eventos',[
            'usuarios'=>$usuarios,
            'eventos'=>$eventos, 
            'eventoAgrupado'=>$eventoAgrupado,
            'usuarioSelected'=>$usuarioSelected
          
          ]);
    }

    public function actionCrearevento(){
      Yii::$app->db->createCommand()->insert('tbl_hv_eventos',[
        "nombre_evento" => Yii::$app->request->post('nombre_evento'),
        "tipo_evento"   => Yii::$app->request->post('tipo_evento'),
        "fecha_evento"  => Yii::$app->request->post('fecha_evento'),
        "ciudad_evento" => Yii::$app->request->post('ciudad_evento'),
        "user_id"  => Yii::$app->request->post('idhvinforpersonal'),
      ])->execute();

        $message  = "<html><body>";
        $message .= "<h3>Se ha realizado Correctamente la creacion de un nuevo Evento</h3>";
        $message .= "<p><b>Evento: </b>".Yii::$app->request->post('nombre_evento') ."</p>";
        $message .= "<p><b>Fecha del evento: </b>".Yii::$app->request->post('fecha_evento') ."</p>";
        $message .= "<p><b>Tipo: </b>".Yii::$app->request->post('tipo_evento') ."</p>";
        $message .= "<p><b>Ciudad: </b>".Yii::$app->request->post('ciudad_evento') ."</p>";
        $message .= "</body></html>";

      Yii::$app->mailer->compose()
      ->setTo('engie.guerrero@grupokonecta.com')
      ->setFrom(Yii::$app->params['email_satu_from'])
      ->setSubject("Creacion Eventos Maestro Cliente CXM")
      // ->attach($tmpFile)
      ->setHtmlBody($message)
      ->send(); 
      Yii::$app->session->setFlash('eventos','Evento Creado Exitosamente');
       return $this->redirect(['eventos', 'id'=>Yii::$app->request->post('idhvinforpersonal')]);
    }

    public function actionEliminarevento($id ,$id_user){
      $evento = Yii::$app->db->createCommand('select * from tbl_hv_eventos where id=:id')
                         ->bindParam(':id',$id)->queryOne();

      Yii::$app->db->createCommand('DELETE FROM tbl_hv_eventos WHERE id=:id')
                   ->bindParam(':id',$id)
                   ->execute();

        $message  = "<html><body>";
        $message .= "<h3>Se ha realizado Correctamente la Eliminacion de un Evento</h3>";
        $message .= "<p><b>Evento: </b>". $evento['nombre_evento'] ."</p>";
        $message .= "<p><b>Fecha del evento: </b>". $evento['fecha_evento'] ."</p>";
        $message .= "<p><b>Tipo: </b>". $evento['tipo_evento'] ."</p>";
        $message .= "<p><b>Ciudad: </b>".$evento['ciudad_evento'] ."</p>";
        $message .= "</body></html>";

      Yii::$app->mailer->compose()
      ->setTo('engie.guerrero@grupokonecta.com')
      ->setFrom(Yii::$app->params['email_satu_from'])
      ->setSubject("Eliminacion Eventos Maestro Cliente CXM")
      // ->attach($tmpFile)
      ->setHtmlBody($message)
      ->send(); 
        Yii::$app->session->setFlash('eventos','Evento Eliminado Exitosamente');
       return $this->redirect(['eventos','id'=>$id_user]);
    }

    public function actionEditarevento($id){
      $eventoOne =Yii::$app->db->createCommand('SELECT * FROM tbl_hv_eventos WHERE id=:id')->bindParam(':id',$id)->queryOne();

       $usuarioSelect =Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal WHERE usua_id=:id')->bindParam(':id',$id)->queryOne();
       $usuarios =Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal')->queryAll();

       return $this->render('editar_evento',[
         'eventoOne' => $eventoOne,
         'usuarioSelect'=> $usuarioSelect,
         'usuarios'=>   $usuarios

       ]);
    }

    public function actionUpdateevento($id){
    
        Yii::$app->db->createCommand()->update('tbl_hv_eventos',[
          "nombre_evento" => Yii::$app->request->post('nombre_evento'),
          "tipo_evento"   => Yii::$app->request->post('tipo_evento'),
          "fecha_evento"  => Yii::$app->request->post('fecha_evento'),
          "ciudad_evento" => Yii::$app->request->post('ciudad_evento'),
          "asistencia" => Yii::$app->request->post('asistencia'),
          "user_id" => Yii::$app->request->post('user_id'),
        ],"id=:id")->bindParam(':id',$id)->execute();
        Yii::$app->session->setFlash('eventos','Evento Editado Exitosamente');
        return $this->redirect(['eventos','id'=>Yii::$app->request->post('user_id')]); 
    }

     public function actionhobbies($search = null){
          $data = Yii::$app->db->createCommand('select * from hobbies')->queryAll();
          $out['results'] = array_values($data);
          echo \yii\helpers\Json::encode($out);

    } 

     public function actionUpload(){
         return var_dump(Yii::$app->request->post('file'));
     }


     public function actionExport(){
          $modelos = new HvInfopersonal();
          if($modelos->load(Yii::$app->request->post())){
              $modelos->file = UploadedFile::getInstance($modelos, 'file');
              $ruta = 'archivos/'.time()."_".$modelos->file->baseName. ".".$modelos->file->extension;
              $modelos->file->saveAs( $ruta ); 
              $this->Importexcel($ruta); 
          }

    
          Yii::$app->session->setFlash('file','archivo cargado exitosamente');
          unlink( $ruta);
          return $this->redirect(['index']);
     }

     public function Importexcel($name){
          $inputFile  = $name;
      
          try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
          } catch (Exception $e) {
            die('Error');
          }
      
          $sheet = $objPHPExcel->getSheet(0);
          $highestRow = $sheet->getHighestRow();
      
          for ($row = 3; $row <= $highestRow; $row++) {
            Yii::$app->db->createCommand()->insert('tbl_hv_infopersonal',[                                        
                                              'hvnombre'=> $sheet->getCell("A".$row)->getValue(),
                                              'hvmovil'=> $sheet->getCell("B".$row)->getValue(),
                                              'hvemailcorporativo'=> $sheet->getCell("D".$row)->getValue(),
                                              'rol'=> $sheet->getCell("E".$row)->getValue(),
                                              'afinidad'=> $sheet->getCell("F".$row)->getValue(),
                                              'nivel'=> $sheet->getCell("G".$row)->getValue(),
                                              'tipo'=> $sheet->getCell("H".$row)->getValue(),
                                              'hvdireccionoficina'=> $sheet->getCell("I".$row)->getValue(),
                                              'hvciudad'=> $sheet->getCell("J".$row)->getValue(),
                                              'director'=> $sheet->getCell("M".$row)->getValue(),
                                              'gerente'=> $sheet->getCell("N".$row)->getValue(),
                                              'cliente'=> $sheet->getCell("O".$row)->getValue(),
                                              'pcrc'=> $sheet->getCell("P".$row)->getValue(),
                                              'estado'=> $sheet->getCell("X".$row)->getValue(),
                                              'hvpais'=> $sheet->getCell("Y".$row)->getValue(),
                                              'hvsusceptible'=> $sheet->getCell("Z".$row)->getValue(),
                                              'hvautorizacion'=> $sheet->getCell("AA".$row)->getValue(),
                                              'hvdireccioncasa'=> $sheet->getCell("AB".$row)->getValue(),
                                              'hvmodalidatrabajo'=> $sheet->getCell("AC".$row)->getValue(),
                                              'usua_id'=>Yii::$app->user->identity->id
            ])->execute();  
          }
     }

     public function actionEliminarprofesion(){
      
        Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
        ->bindParam(':id',Yii::$app->request->post('profesion') )
        ->execute();
        Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
        return $this->redirect(['index']);
     }
     
     public function actionEliminarespecializacion(){
        Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
        ->bindParam(':id',Yii::$app->request->post('especializacion') )
        ->execute();
        Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
        return $this->redirect(['index']);
     }

     public function actionEliminarmaestria(){
        Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
        ->bindParam(':id',Yii::$app->request->post('maestria') )
        ->execute();
        Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');

        return $this->redirect(['index']);
     }

     public function actionEliminardoctorado(){
        Yii::$app->db->createCommand('DELETE FROM tbl_hv_cursosacademico WHERE idhvcursosacademico=:id')
        ->bindParam(':id',Yii::$app->request->post('doctorado') )
        ->execute();
        Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
        return $this->redirect(['index']);
     }

     public function actionExcelexportadmin(){

      $varCorreo = Yii::$app->request->post("email");


      $varlistusuarios = Yii::$app->db->createCommand("SELECT u.cliente,u.director, u.gerente , u.pcrc,
      u.hvnombre, u.hvidentificacion,u.hvdireccionoficina, u.hvdireccioncasa,u.hvemailcorporativo, u.hvmovil, u.hvcontactooficina,
      u.hvpais, u.hvciudad, u.hvmodalidatrabajo,u.hvautorizacion, u.hvsatu, u.areatrabajo,u.rol, u.antiguedadrol,u.fechacontacto,
      u.tipo, u.nivel, u.afinidad, u.nombrejefe, u.cargojefe, u.rolanterior, u.profesion, u.especializacion, u.maestria, u.doctorado,
      u.estadocivil, u.numerohijos,u.dominancia, u.estilosocial, u.hobbies, u.gustos as intereses FROM tbl_hv_infopersonal u")->queryAll();

      $phpExc = new \PHPExcel();
      $phpExc->getProperties()
              ->setCreator("Konecta")
              ->setLastModifiedBy("Konecta")
              ->setTitle("Lista de usuarios - Evaluacion Desarrollo")
              ->setSubject("Evaluacion de Desarrollo")
              ->setDescription("Este archivo contiene el listado de los usuarios registrados para maetsro cliente")
              ->setKeywords("Lista de usuarios");
      $phpExc->setActiveSheetIndex(0);
     
      $phpExc->getActiveSheet()->setShowGridlines(False);

      $styleArray = array(
              'alignment' => array(
                  'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
          );

      $styleColor = array( 
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => '28559B'),
              )
          );

      $styleArrayTitle = array(
              'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'FFFFFF')
              )
          );

      $styleArraySubTitle2 = array(              
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => 'C6C6C6'),
              )
          );  

      // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
      $styleArrayBody = array(
              'font' => array(
                  'bold' => false,
                  'color' => array('rgb' => '2F4F4F')
              ),
              'borders' => array(
                  'allborders' => array(
                      'style' => \PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('rgb' => 'DDDDDD')
                  )
              )
          );


      $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

      $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
      $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
      $phpExc->setActiveSheetIndex(0)->mergeCells('A1:AJ1');

      $phpExc->getActiveSheet()->SetCellValue('A2','Cliente');
      $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('B2','Director');
      $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('C2','Gerente');
      $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('D2','Pcrc');
      $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('E2','Nombre');
      $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('F2','Identificacion');
      $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('G2','Direccion Oficina');
      $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('H2','Direccion Casa');
      $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('I2','Email Corporativo');
      $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('J2','Celular');
      $phpExc->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('K2','Contacto Oficina');
      $phpExc->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('L2','Pais');
      $phpExc->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('M2','Cuidad');
      $phpExc->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('N2','Modalidad de trabajo');
      $phpExc->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('O2','Autorizacion');
      $phpExc->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('P2','Satu');
      $phpExc->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('Q2','Area de Trabajo');
      $phpExc->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('R2','Rol');
      $phpExc->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('S2','Antiguedad');
      $phpExc->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('T2','Fecha Contacto');
      $phpExc->getActiveSheet()->getStyle('T2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('U2','Tipo');
      $phpExc->getActiveSheet()->getStyle('U2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('V2','Nivel');
      $phpExc->getActiveSheet()->getStyle('V2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('W2','Afinidad');
      $phpExc->getActiveSheet()->getStyle('W2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('X2','Nombre Jefe');
      $phpExc->getActiveSheet()->getStyle('X2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('Y2','Cargo Jefe');
      $phpExc->getActiveSheet()->getStyle('Y2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('Z2','Rol Anterior');
      $phpExc->getActiveSheet()->getStyle('Z2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AA2','Profesion');
      $phpExc->getActiveSheet()->getStyle('AA2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AB2','Especializacion');
      $phpExc->getActiveSheet()->getStyle('AB2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AC2','Maestria');
      $phpExc->getActiveSheet()->getStyle('AC2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AD2','Doctorado');
      $phpExc->getActiveSheet()->getStyle('AD2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AE2','Estado Civil');
      $phpExc->getActiveSheet()->getStyle('AE2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AF2','Numero de Hijos');
      $phpExc->getActiveSheet()->getStyle('AF2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleArraySubTitle2);

      
      $phpExc->getActiveSheet()->SetCellValue('AG2','Dominancia');
      $phpExc->getActiveSheet()->getStyle('AG2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleArraySubTitle2);

      
      $phpExc->getActiveSheet()->SetCellValue('AH2','Estilo Social');
      $phpExc->getActiveSheet()->getStyle('AH2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AI2','Hobbies');
      $phpExc->getActiveSheet()->getStyle('AI2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AJ2','Gustos');
      $phpExc->getActiveSheet()->getStyle('AJ2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AJ2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AJ2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AJ2')->applyFromArray($styleArraySubTitle2);



      $numCell = 3;
      foreach ($varlistusuarios as $key => $value) {
        $numCell++;

        $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['cliente']); 
        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['director']); 
        $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['gerente']);
        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['pcrc']);

        $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['hvnombre']); 
        $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['hvidentificacion']); 
        $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['hvdireccionoficina']); 
        $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['hvdireccioncasa']); 
        $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['hvemailcorporativo']); 
        $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $value['hvmovil']); 
        $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $value['hvcontactooficina']);
        $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $value['hvpais']);

        $phpExc->getActiveSheet()->setCellValue('M'.$numCell, $value['hvciudad']); 
        $phpExc->getActiveSheet()->setCellValue('N'.$numCell, $value['hvmodalidatrabajo']); 
        $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $value['hvautorizacion']); 
        $phpExc->getActiveSheet()->setCellValue('P'.$numCell, $value['hvsatu']); 
        $phpExc->getActiveSheet()->setCellValue('Q'.$numCell, $value['areatrabajo']); 
        $phpExc->getActiveSheet()->setCellValue('R'.$numCell, $value['rol']); 
        $phpExc->getActiveSheet()->setCellValue('S'.$numCell, $value['antiguedadrol']);
        $phpExc->getActiveSheet()->setCellValue('T'.$numCell, $value['fechacontacto']);

        $phpExc->getActiveSheet()->setCellValue('U'.$numCell, $value['tipo']); 
        $phpExc->getActiveSheet()->setCellValue('V'.$numCell, $value['nivel']); 
        $phpExc->getActiveSheet()->setCellValue('W'.$numCell, $value['afinidad']); 
        $phpExc->getActiveSheet()->setCellValue('X'.$numCell, $value['nombrejefe']); 
        $phpExc->getActiveSheet()->setCellValue('Y'.$numCell, $value['cargojefe']); 
        $phpExc->getActiveSheet()->setCellValue('Z'.$numCell, $value['rolanterior']); 
        $phpExc->getActiveSheet()->setCellValue('AA'.$numCell, $value['profesion']);
        $phpExc->getActiveSheet()->setCellValue('AB'.$numCell, $value['especializacion']);

        $phpExc->getActiveSheet()->setCellValue('AC'.$numCell, $value['maestria']); 
        $phpExc->getActiveSheet()->setCellValue('AD'.$numCell, $value['doctorado']); 
        $phpExc->getActiveSheet()->setCellValue('AE'.$numCell, $value['estadocivil']); 
        $phpExc->getActiveSheet()->setCellValue('AF'.$numCell, $value['numerohijos']); 
        $phpExc->getActiveSheet()->setCellValue('AG'.$numCell, $value['dominancia']); 
        $phpExc->getActiveSheet()->setCellValue('AH'.$numCell, $value['estilosocial']); 
        $phpExc->getActiveSheet()->setCellValue('AI'.$numCell, $value['hobbies']); 
        $phpExc->getActiveSheet()->setCellValue('AJ'.$numCell, $value['intereses']); 


      }
      $numCell = $numCell;

      $hoy = getdate();
      $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
            
      $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
              
      $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
      $tmpFile.= ".xls";

      $objWriter->save($tmpFile);

      $message = "<html><body>";
      $message .= "<h3>Adjunto del archivo listado usuario maestro cliente</h3>";
      $message .= "</body></html>";

      Yii::$app->mailer->compose()
                      ->setTo($varCorreo)
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject("Envio Listado de usuarios registrado - Hoja de Vida")
                      ->attach($tmpFile)
                      ->setHtmlBody($message)
                      ->send();

       Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
      return $this->redirect(['index']);

     }

     public function actionExcelexport(){

      $varCorreo = Yii::$app->request->post("email");

      $sessiones = Yii::$app->user->identity->id;

      $varlistusuarios = Yii::$app->db->createCommand("SELECT u.cliente,u.director, u.gerente , u.pcrc,
      u.hvnombre, u.hvidentificacion,u.hvdireccionoficina, u.hvdireccioncasa,u.hvemailcorporativo, u.hvmovil, u.hvcontactooficina,
      u.hvpais, u.hvciudad, u.hvmodalidatrabajo,u.hvautorizacion, u.hvsatu, u.areatrabajo,u.rol, u.antiguedadrol,u.fechacontacto,
      u.tipo, u.nivel, u.afinidad, u.nombrejefe, u.cargojefe, u.rolanterior, u.profesion, u.especializacion, u.maestria, u.doctorado,
      u.estadocivil, u.numerohijos,u.dominancia, u.estilosocial, u.hobbies, u.gustos as intereses FROM tbl_hv_infopersonal u 
      WHERE u.usua_id = :id")->bindParam(':id',$sessiones)->queryAll();


      $phpExc = new \PHPExcel();
      $phpExc->getProperties()
              ->setCreator("Konecta")
              ->setLastModifiedBy("Konecta")
              ->setTitle("Lista de usuarios - Evaluaci n Desarrollo")
              ->setSubject("Evaluaci n de Desarrollo")
              ->setDescription("Este archivo contiene el listado de los usuarios registrados para maetsro cliente")
              ->setKeywords("Lista de usuarios");
      $phpExc->setActiveSheetIndex(0);

      $phpExc->getActiveSheet()->setShowGridlines(False);

      $styleArray = array(
              'alignment' => array(
                  'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
          );

      $styleColor = array( 
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => '28559B'),
              )
          );

      $styleArrayTitle = array(
              'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'FFFFFF')
              )
          );

      $styleArraySubTitle2 = array(              
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => 'C6C6C6'),
              )
          );  

      // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
      $styleArrayBody = array(
              'font' => array(
                  'bold' => false,
                  'color' => array('rgb' => '2F4F4F')
              ),
              'borders' => array(
                  'allborders' => array(
                      'style' => \PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('rgb' => 'DDDDDD')
                  )
              )
          );

      $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

      $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
      $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
      $phpExc->setActiveSheetIndex(0)->mergeCells('A1:AJ1');

      $phpExc->getActiveSheet()->SetCellValue('A2','Cliente');
      $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('B2','Director');
      $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('C2','Gerente');
      $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('D2','Pcrc');
      $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('E2','Nombre');
      $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('F2','Identificacion');
      $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('G2','Direccion Oficina');
      $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('H2','Direccion Casa');
      $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('I2','Email Corporativo');
      $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('J2','Celular');
      $phpExc->getActiveSheet()->getStyle('J2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('J2')->applyFromArray($styleArraySubTitle2);



      $phpExc->getActiveSheet()->SetCellValue('K2','Contacto Oficina');
      $phpExc->getActiveSheet()->getStyle('K2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('K2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('L2','Pais');
      $phpExc->getActiveSheet()->getStyle('L2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('L2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('M2','Cuidad');
      $phpExc->getActiveSheet()->getStyle('M2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('M2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('N2','Modalidad de trabajo');
      $phpExc->getActiveSheet()->getStyle('N2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('N2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('O2','Autorizacion');
      $phpExc->getActiveSheet()->getStyle('O2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('O2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('P2','Satu');
      $phpExc->getActiveSheet()->getStyle('P2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('P2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('Q2','Area de Trabajo');
      $phpExc->getActiveSheet()->getStyle('Q2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Q2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('R2','Rol');
      $phpExc->getActiveSheet()->getStyle('R2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('R2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('S2','Antiguedad');
      $phpExc->getActiveSheet()->getStyle('S2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('S2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('T2','Fecha Contacto');
      $phpExc->getActiveSheet()->getStyle('T2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('T2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('U2','Tipo');
      $phpExc->getActiveSheet()->getStyle('U2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('U2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('V2','Nivel');
      $phpExc->getActiveSheet()->getStyle('V2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('V2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('W2','Afinidad');
      $phpExc->getActiveSheet()->getStyle('W2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('W2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('X2','Nombre Jefe');
      $phpExc->getActiveSheet()->getStyle('X2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('X2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('Y2','Cargo Jefe');
      $phpExc->getActiveSheet()->getStyle('Y2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Y2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('Z2','Rol Anterior');
      $phpExc->getActiveSheet()->getStyle('Z2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('Z2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AA2','Profesion');
      $phpExc->getActiveSheet()->getStyle('AA2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AA2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AB2','Especializacion');
      $phpExc->getActiveSheet()->getStyle('AB2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AB2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AC2','Maestria');
      $phpExc->getActiveSheet()->getStyle('AC2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AD2','Doctorado');
      $phpExc->getActiveSheet()->getStyle('AD2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AD2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AE2','Estado Civil');
      $phpExc->getActiveSheet()->getStyle('AE2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AE2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('AF2','Numero de Hijos');
      $phpExc->getActiveSheet()->getStyle('AF2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AF2')->applyFromArray($styleArraySubTitle2);

      
      $phpExc->getActiveSheet()->SetCellValue('AG2','Dominancia');
      $phpExc->getActiveSheet()->getStyle('AG2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AG2')->applyFromArray($styleArraySubTitle2);

      
      $phpExc->getActiveSheet()->SetCellValue('AH2','Estilo Social');
      $phpExc->getActiveSheet()->getStyle('AH2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AI2','Hobbies');
      $phpExc->getActiveSheet()->getStyle('AI2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AI2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('AJ2','Gustos');
      $phpExc->getActiveSheet()->getStyle('AJ2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('AJ2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('AJ2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('AJ2')->applyFromArray($styleArraySubTitle2);



      $numCell = 3;
      foreach ($varlistusuarios as $key => $value) {
        $numCell++;

        $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['cliente']); 
        $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['director']); 
        $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['gerente']);
        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['pcrc']);

        $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['hvnombre']); 
        $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['hvidentificacion']); 
        $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['hvdireccionoficina']); 
        $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['hvdireccioncasa']); 
        $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['hvemailcorporativo']); 
        $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $value['hvmovil']); 
        $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $value['hvcontactooficina']);
        $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $value['hvpais']);

        $phpExc->getActiveSheet()->setCellValue('M'.$numCell, $value['hvciudad']); 
        $phpExc->getActiveSheet()->setCellValue('N'.$numCell, $value['hvmodalidatrabajo']); 
        $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $value['hvautorizacion']); 
        $phpExc->getActiveSheet()->setCellValue('P'.$numCell, $value['hvsatu']); 
        $phpExc->getActiveSheet()->setCellValue('Q'.$numCell, $value['areatrabajo']); 
        $phpExc->getActiveSheet()->setCellValue('R'.$numCell, $value['rol']); 
        $phpExc->getActiveSheet()->setCellValue('S'.$numCell, $value['antiguedadrol']);
        $phpExc->getActiveSheet()->setCellValue('T'.$numCell, $value['fechacontacto']);

        $phpExc->getActiveSheet()->setCellValue('U'.$numCell, $value['tipo']); 
        $phpExc->getActiveSheet()->setCellValue('V'.$numCell, $value['nivel']); 
        $phpExc->getActiveSheet()->setCellValue('W'.$numCell, $value['afinidad']); 
        $phpExc->getActiveSheet()->setCellValue('X'.$numCell, $value['nombrejefe']); 
        $phpExc->getActiveSheet()->setCellValue('Y'.$numCell, $value['cargojefe']); 
        $phpExc->getActiveSheet()->setCellValue('Z'.$numCell, $value['rolanterior']); 
        $phpExc->getActiveSheet()->setCellValue('AA'.$numCell, $value['profesion']);
        $phpExc->getActiveSheet()->setCellValue('AB'.$numCell, $value['especializacion']);

        $phpExc->getActiveSheet()->setCellValue('AC'.$numCell, $value['maestria']); 
        $phpExc->getActiveSheet()->setCellValue('AD'.$numCell, $value['doctorado']); 
        $phpExc->getActiveSheet()->setCellValue('AE'.$numCell, $value['estadocivil']); 
        $phpExc->getActiveSheet()->setCellValue('AF'.$numCell, $value['numerohijos']); 
        $phpExc->getActiveSheet()->setCellValue('AG'.$numCell, $value['dominancia']); 
        $phpExc->getActiveSheet()->setCellValue('AH'.$numCell, $value['estilosocial']); 
        $phpExc->getActiveSheet()->setCellValue('AI'.$numCell, $value['hobbies']); 
        $phpExc->getActiveSheet()->setCellValue('AJ'.$numCell, $value['intereses']); 


      }
      $numCell = $numCell;

      $hoy = getdate();
      $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
            
      $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
              
      $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
      $tmpFile.= ".xls";

      $objWriter->save($tmpFile);

      $message = "<html><body>";
      $message .= "<h3>Adjunto del archivo listado usuario maestro cliente</h3>";
      $message .= "</body></html>";

      Yii::$app->mailer->compose()
                      ->setTo($varCorreo)
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject("Envio Listado de usuarios registrado - Hoja de Vida")
                      ->attach($tmpFile)
                      ->setHtmlBody($message)
                      ->send();

       Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
      return $this->redirect(['index']);

     }

     public function actionExcelexporteventosadmin(){
      $varCorreo = Yii::$app->request->post("email");


      $evento =Yii::$app->db->createCommand("SELECT u.director, u.gerente, u.cliente, u.hvnombre, u.hvidentificacion, 
      e.nombre_evento, e.tipo_evento,e.ciudad_evento, e.fecha_evento 
      FROM tbl_hv_infopersonal u
      INNER JOIN tbl_hv_eventos e ON u.idhvinforpersonal= e.user_id")->queryAll();

      $phpExc = new \PHPExcel();
      $phpExc->getProperties()
              ->setCreator("Konecta")
              ->setLastModifiedBy("Konecta")
              ->setTitle("Lista de usuarios - Evaluaci n Desarrollo")
              ->setSubject("Evaluaci n de Desarrollo")
              ->setDescription("Este archivo contiene el listado de los usuarios registrados para maetsro cliente")
              ->setKeywords("Lista de usuarios");
      $phpExc->setActiveSheetIndex(0);
     
      $phpExc->getActiveSheet()->setShowGridlines(False);

      $styleArray = array(
              'alignment' => array(
                  'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
          );

      $styleColor = array( 
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => '28559B'),
              )
          );

      $styleArrayTitle = array(
              'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'FFFFFF')
              )
          );

      $styleArraySubTitle2 = array(              
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => 'C6C6C6'),
              )
          );  

      // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
      $styleArrayBody = array(
              'font' => array(
                  'bold' => false,
                  'color' => array('rgb' => '2F4F4F')
              ),
              'borders' => array(
                  'allborders' => array(
                      'style' => \PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('rgb' => 'DDDDDD')
                  )
              )
          );

      $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);


      $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
      $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
      $phpExc->setActiveSheetIndex(0)->mergeCells('A1:I1');

      $phpExc->getActiveSheet()->SetCellValue('A2','Cliente');
      $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('B2','Director');
      $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('C2','Gerente');
      $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('D2','Nombre');
      $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('E2','Identificacion');
      $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('F2','Nombre Evento');
      $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('G2','Tipo Evento');
      $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('H2','Ciudad Evento');
      $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('I2','fecha Evento');
      $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);

     
        $numCell = 3;

        foreach ($evento as $key => $value) {
          $numCell++;
  
          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['director']); 
          $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['gerente']); 
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['cliente']);
          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['hvnombre']);
  
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['hvidentificacion']); 
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['nombre_evento']); 
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['tipo_evento']);     
          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['ciudad_evento']); 
          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['fecha_evento']); 
  
        }

      $numCell = $numCell;

      $hoy = getdate();
      $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
            
      $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
              
      $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
      $tmpFile.= ".xls";

      $objWriter->save($tmpFile);

      $message = "<html><body>";
      $message .= "<h3>Adjunto del archivo listado usuario maestro cliente</h3>";
      $message .= "</body></html>";

      Yii::$app->mailer->compose()
                      ->setTo($varCorreo)
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject("Envio Listado de usuarios registrado - Hoja de Vida")
                      ->attach($tmpFile)
                      ->setHtmlBody($message)
                      ->send();

       Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
      return $this->redirect(['index']);
     }

     public function actionExcelexporteventos(){
      $varCorreo = Yii::$app->request->post("email");
      $sessiones = Yii::$app->user->identity->id;


      $evento =Yii::$app->db->createCommand("SELECT u.director, u.gerente, u.cliente, u.hvnombre, u.hvidentificacion, 
      e.nombre_evento, e.tipo_evento,e.ciudad_evento, e.fecha_evento 
      FROM tbl_hv_infopersonal u
      INNER JOIN tbl_hv_eventos e ON u.idhvinforpersonal= e.user_id WHERE u.usua_id=:id")->bindParam(':id',$sessiones)->queryAll();

      $phpExc = new \PHPExcel();
      $phpExc->getProperties()
              ->setCreator("Konecta")
              ->setLastModifiedBy("Konecta")
              ->setTitle("Lista de usuarios - Evaluaci n Desarrollo")
              ->setSubject("Evaluaci n de Desarrollo")
              ->setDescription("Este archivo contiene el listado de los usuarios registrados para maetsro cliente")
              ->setKeywords("Lista de usuarios");
      $phpExc->setActiveSheetIndex(0);
     
      $phpExc->getActiveSheet()->setShowGridlines(False);

      $styleArray = array(
              'alignment' => array(
                  'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              ),
          );

      $styleColor = array( 
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => '28559B'),
              )
          );

      $styleArrayTitle = array(
              'font' => array(
                'bold' => false,
                'color' => array('rgb' => 'FFFFFF')
              )
          );

      $styleArraySubTitle2 = array(              
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => 'C6C6C6'),
              )
          );  

      // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
      $styleArrayBody = array(
              'font' => array(
                  'bold' => false,
                  'color' => array('rgb' => '2F4F4F')
              ),
              'borders' => array(
                  'allborders' => array(
                      'style' => \PHPExcel_Style_Border::BORDER_THIN,
                      'color' => array('rgb' => 'DDDDDD')
                  )
              )
          );

      $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);


      $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
      $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
      $phpExc->setActiveSheetIndex(0)->mergeCells('A1:H1');

      $phpExc->getActiveSheet()->SetCellValue('A2','Cliente');
      $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('B2','Director');
      $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('C2','Gerente');
      $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('D2','Nombre');
      $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('E2','Identificacion');
      $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('F2','Nombre Evento');
      $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);

      $phpExc->getActiveSheet()->SetCellValue('G2','Tipo Evento');
      $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('H2','Ciudad Evento');
      $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);


      $phpExc->getActiveSheet()->SetCellValue('I2','fecha Evento');
      $phpExc->getActiveSheet()->getStyle('I2')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('I2')->applyFromArray($styleArraySubTitle2);

     
        $numCell = 3;

        foreach ($evento as $key => $value) {
          $numCell++;
  
          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['director']); 
          $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['gerente']); 
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['cliente']);
          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['hvnombre']);
  
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['hvidentificacion']); 
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['nombre_evento']); 
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['tipo_evento']);     
          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['ciudad_evento']); 
          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $value['fecha_evento']); 
  
        }

      $numCell = $numCell;

      $hoy = getdate();
      $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
            
      $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
              
      $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
      $tmpFile.= ".xls";

      $objWriter->save($tmpFile);

      $message = "<html><body>";
      $message .= "<h3>Adjunto del archivo listado usuario maestro cliente</h3>";
      $message .= "</body></html>";

      Yii::$app->mailer->compose()
                      ->setTo($varCorreo)
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject("Envio Listado de usuarios registrado - Hoja de Vida")
                      ->attach($tmpFile)
                      ->setHtmlBody($message)
                      ->send();

       Yii::$app->session->setFlash('file','Correo Enviado Exitosamente');
      return $this->redirect(['index']);
     }

     public function actionCrearmodalidad(){
      $modalidad = Yii::$app->db->createCommand('select * from tbl_hv_modalidad_trabajo')->queryAll();
      return $this->render('modalidad',[ "modalidad"=> $modalidad ]);
    }

    public function actionGuardarmodalidad(){
       Yii::$app->db->createCommand()->insert('tbl_hv_modalidad_trabajo',[
           "modalidad"=>Yii::$app->request->post('modalidad'),
           "usua_id"  =>Yii::$app->user->identity->id
       ])->execute();
       Yii::$app->session->setFlash('info','MODALIDAD CREADA EXITOSAMENTE');
       return $this->redirect(["crearmodalidad"]);
    }


    public function actionEliminarmodalidad($id){
      Yii::$app->db->createCommand('DELETE FROM tbl_hv_modalidad_trabajo WHERE hv_idmodalidad=:id')->bindParam(':id',$id)->execute();
      Yii::$app->session->setFlash('info','MODALIDAD ELIMINADA CORRECTAMENTE');
      return $this->redirect(["crearmodalidad"]);
    }

    public function actionResumen($id){

      $sessiones = Yii::$app->user->identity->id;

      $rol =  new Query;
      $rol     ->select(['tbl_roles.role_id'])
                  ->from('tbl_roles')
                  ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                              'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                  ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                              'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                  ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
      $command = $rol->createCommand();
      $roles = $command->queryScalar();
    

       $clients = Yii::$app->db->createCommand('SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
       WHERE i.usua_id=:id')->bindParam(':id', $id)->queryAll();

      $decisor = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE i.usua_id=:id  AND i.tipo='Decisor' ")->bindParam(':id', $id)->queryAll();

      $estrategico = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE i.usua_id=:id  AND i.nivel='Estrategico' ")->bindParam(':id', $id)->queryAll();


      $operativo = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE i.usua_id=:id  AND i.nivel='Operativo' ")->bindParam(':id', $id)->queryAll();



      $clientsAdmin = Yii::$app->db->createCommand('SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i')->queryAll();

      $clientsAdmins = Yii::$app->db->createCommand('SELECT * FROM tbl_hv_infopersonal')->queryAll();

      $decisorAdmin = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE  i.tipo='Decisor' ")->bindParam(':id', $id)->queryAll();

      $estrategicoAdmin = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE  i.nivel='Estrategico' ")->bindParam(':id', $id)->queryAll();


      $operativoAdmin = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
      WHERE  i.nivel='Operativo' ")->bindParam(':id', $id)->queryAll();



        $decisorEstrategico =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='estrategico' ")->queryAll();

        $decisorOperativo=Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='operativo' ")->queryAll();


        $nodecisorEstrategico =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='estrategico' ")->queryAll();


        $nodecisorOperativo = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='operativo' ")->queryAll();


        $clienteInteresAdmin =  Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.afinidad='de interes' ")->queryAll();


        $clienteInteres =  Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.afinidad='de interes' AND i.usua_id=:id ")->bindParam(':id', $id)->queryAll();


        $decisorEstrategicoU =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='estrategico' AND i.usua_id=:id  ")->bindParam(':id', $id)->queryAll();

        $decisorOperativoU=Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='Decisor' AND i.nivel='operativo' AND i.usua_id=:id  ")->bindParam(':id', $id)->queryAll();


        $nodecisorEstrategicoU =Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='estrategico' AND i.usua_id=:id ")->bindParam(':id', $id)->queryAll();


        $nodecisorOperativoU = Yii::$app->db->createCommand("SELECT COUNT(i.idhvinforpersonal) AS total FROM tbl_hv_infopersonal i
        WHERE  i.tipo='No Decisor' AND i.nivel='operativo' AND i.usua_id=:id  ")->bindParam(':id', $id)->queryAll();



       return $this->render('resumen',[
          'clients'=>$clients,
          'decisor'=>$decisor,
          'estrategico'=>$estrategico,
          'operativo' =>$operativo,
          'clientsAdmin'=>$clientsAdmin,
          'decisorAdmin'=>$decisorAdmin,
          'estrategicoAdmin'=>$estrategicoAdmin,
          'operativoAdmin' =>$operativoAdmin,
          'id'=>$id,
          'roles' =>$roles,
          'clientsAdmins'=>$clientsAdmins,

          'decisorEstrategico'=>$decisorEstrategico,
          'decisorOperativo'=>$decisorOperativo,
          'nodecisorEstrategico'=>$nodecisorEstrategico,
          'nodecisorOperativo'=>$nodecisorOperativo,

          'decisorEstrategicoU'=>$decisorEstrategicoU,
          'decisorOperativoU'=>$decisorOperativoU,
          'nodecisorEstrategicoU'=>$nodecisorEstrategicoU,
          'nodecisorOperativoU'=>$nodecisorOperativoU,
          'clienteInteresAdmin'=>$clienteInteresAdmin,
          'clienteInteres'=>$clienteInteres
        ]);
    }


}

?>


