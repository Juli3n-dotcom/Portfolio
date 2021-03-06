<?php
require_once __DIR__ . '/../../config/bootstrap_admin.php';
require_once __DIR__ . '/../../functions/education_functions.php';

/* #############################################################################

Ajout d'une formation a partir education.php en Ajax

############################################################################# */

$result = array(); 

if(!empty($_POST)){ 

  $titre = $_POST['add_name_edu'];
  $school = $_POST['add_name_school'];
  $contenu = $_POST['add_contenu'];
  $url = $_POST['add_url'];

  if(empty($titre)){

    $result['status'] = false;
    $result['notif'] = notif('warning','oups! il manque le titre'); 

  }elseif(getEduBy($pdo,'titre',$titre)!==null){

    $result['status'] = false;
    $result['notif'] = notif('warning','oups! cette formation existe déjà'); 

  }elseif(empty($contenu)){

    $result['status'] = false;
    $result['notif'] = notif('warning','oups! il manque une description'); 

  }elseif(empty($url)){

    $result['status'] = false;
    $result['notif'] = notif('warning','oups! il manque l\'adresse du site'); 

  }else{

    $req = $pdo->prepare('INSERT INTO education (titre, school, contenu, url, start_date, stop_date, est_publie)
                          VALUES(:titre, :school, :contenu, :url, :start_date, :stop_date, :publie)');
                    
    $req->bindParam(':titre',$titre);
    $req->bindParam(':school',$school);
    $req->bindParam(':contenu',$contenu);
    $req->bindParam(':url',$url);
    $req->bindValue(':start_date',(new DateTime($_POST['from']))->format('Y-m-d')); 
    $req->bindValue(':stop_date',(new DateTime($_POST['to']))->format('Y-m-d')); 
    $req->bindValue(':publie',isset($_POST['est_publie']),PDO::PARAM_BOOL);
    $req->execute();

    $result['status'] = true;
    $result['notif'] = notif('success','Nouvelle formation ajoutée');

    // préparation retour Ajax
    $query = $pdo->query('SELECT * FROM education');

    //retour ajax table
    $result['resultat'] = '<table>';

    $result['resultat'] .= '<thead>
                    <tr>
                      <th>ID</th>
                      <th>Titre</th>
                      <th>School</th>
                      <th>Début</th>
                      <th>Fin</th>';
                      if($Membre['statut'] == 0){
                        $result['resultat'] .= ' <th>Publié</th>';
                        $result['resultat'] .= '<th>Actions</th>';
                      }else{
                        $result['resultat'] .= '<th>Action</th>';
                      }
                      
    $result['resultat'] .=  '</tr>
                </thead>';

    $result['resultat'] .= '<tbody>';

    while($edu = $query->fetch()){

      // changement format date
      $date_from = str_replace('/', '-', $edu['start_date']);
      $date_to = str_replace('/', '-', $edu['stop_date']);

      $result['resultat'] .= '<tr>';
      $result['resultat'] .= '<td>'.$edu['id_education'].'</td>';
      $result['resultat'] .= '<td>'.$edu['titre'].'</td>';
      $result['resultat'] .= '<td>'.$edu['school'].'</td>';
      $result['resultat'] .= '<td>'.date('Y', strtotime($date_from)).'</td>';
      $result['resultat'] .= '<td>'.date('Y', strtotime($date_to)).'</td>';

      if($Membre['statut'] == 0){

        if($edu['est_publie'] == 1){

          $result['resultat'] .= '<td> <input type="checkbox" id="est_publie" name="est_publie" class="est_publie" value='.$edu['est_publie'].' checked></td>';

        }else{

          $result['resultat'] .= '<td> <input type="checkbox" id="est_publie" name="est_publie" class="est_publie" value='.$edu['est_publie'].'></td>';

        }
        
        
        $result['resultat'] .= '<td class="member_action">';
            $result['resultat'] .= '<a href='.$edu['url'].' class="linkbtn"></a>';
            $result['resultat'] .= '<input type="button" class="viewbtn" name="view" id="'.$edu['id_education'].'"></input>';
            $result['resultat'] .= '<input type="button" class="editbtn" id="'.$edu['id_education'].'"></input>';
            $result['resultat'] .= '<input type="button" class="deletebtn"></input>';
        $result['resultat'] .= '</td>';

        }else{

          $result['resultat'] .= '<td class="member_action">';
            $result['resultat'] .= '<a href='.$edu['url'].' class="linkbtn"></a>';
          $result['resultat'] .= '</td>';

        }

      $result['resultat'] .= '</tr>';

    }

    $result['resultat'] .= '</tbody>';

    $result['resultat'] .= '</table>';

  }

}
// Return result 
echo json_encode($result);
?>