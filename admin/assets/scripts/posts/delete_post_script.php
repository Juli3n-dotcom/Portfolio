<?php
require_once __DIR__ . '/../../config/bootstrap_admin.php';
require_once __DIR__ . '/../../functions/posts_functions.php';
require_once __DIR__ . '/../../functions/categories_functions.php';

/* #############################################################################

suppression  d'un post a partir posts.php en Ajax

############################################################################# */


if(!empty($_POST)){

  
  $result = array();
  $id = $_POST['id'];
  $img = $_POST['img'];
  $confirme = 'on';

  // validation en back de la confirmation de la suppression
    if(($_POST['confirmedelete']) !== $confirme ){

      $result['status'] = false;
      $result['notif'] = notif('error','Merci de confirmer la suppression');
  
    }else{

      //suppresion du logo
    $data = $pdo->query("SELECT * FROM pics WHERE id_pics = '$img'");
    $photo = $data->fetch(PDO::FETCH_ASSOC);

    $file =__DIR__.'/../../../../global/uploads/';
    $dir = opendir($file);
    unlink($file.$photo['img']);
    closedir($dir);

    $req1 = $pdo->exec("DELETE FROM pics WHERE id_pics = '$img'");

     //suppresion du post de la BDD
    $req2 = $pdo->exec("DELETE FROM posts WHERE id_post = '$id'");

    $result['status'] = true;
    $result['notif'] = notif('success','Post supprimé');

    //retour ajax card
    $result['cards'] = '<div class="card__single">
    <div class="card__body">
      <i class="fas fa-folder-open"></i>
      <div>
        <h5>Tous les posts</h5>
        <h4>'.countPosts($pdo).'</h4>
      </div>
    </div>
    <div class="card__footer">
      <a href="">View all</a>
    </div>
</div>';

$result['cards'] .= '<div class="card__single">
    <div class="card__body">
      <i class="far fa-eye"></i>
      <div>
        <h5>Publiés</h5>
        <h4>'.countPostsPublie($pdo).'</h4>
      </div>
    </div>
    <div class="card__footer">
      <a href="">View all</a>
  </div>
</div>';

$result['cards'] .= '<div class="card__single">
    <div class="card__body">
        <i class="far fa-eye-slash"></i>
      <div>
          <h5>Non Publiés</h5>
          <h4>'. countPostsNonPublie($pdo).'</h4>
      </div>
    </div>
    <div class="card__footer">
      <a href="">View all</a>
    </div>
  </div>';

// préparation retour Ajax
$query = $pdo->query('SELECT * FROM posts');

//retour ajax table
$result['resultat'] = '<table>';

$result['resultat'] .= '<thead>
<tr>
<th>ID</th>
<th class="dnone">pics_id</th>
<th>Img</th>
<th>Titre</th>
<th>Cat</th>
<th>Clics</th>';
if($Membre['statut'] == 0){
  $result['resultat'] .= ' <th>Publié</th>';
  $result['resultat'] .= '<th>Actions</th>';
}else{
  $result['resultat'] .= '<th>Action</th>';
}

$result['resultat'] .=  '</tr>
</thead>';

$result['resultat'] .= '<tbody>';

while($post = $query->fetch()){

$result['resultat'] .= '<tr>';
$result['resultat'] .= '<td>'.$post['id_post'].'</td>';
$result['resultat'] .= '<td class="dnone">'.$post['pics_id'].'</td>';

if($post["pics_id"] != NULL){
$result['resultat'] .= '<td><div class="img-profil" style="background-image: url(../global/uploads/'.getImg($pdo, $post["pics_id"]).'")"></div></td>';
}else{
$result['resultat'] .= '<td> </td>';
}

$result['resultat'] .= '<td>'.$post['titre'].'</td>';
$result['resultat'] .= '<td><div class="td-cat">'.getIcon($pdo, $post["categories_id"]).'</div></td>';
$result['resultat'] .= '<td>'.getClick($pdo, $post["pics_id"]).'</td>';

if($Membre['statut'] == 0){

if($post['est_publie'] == 1){

$result['resultat'] .= '<td> <input type="checkbox" id="est_publie" name="est_publie" class="confirmedelete" value='.$post['est_publie'].' checked></td>';

}else{

$result['resultat'] .= '<td> <input type="checkbox" id="est_publie" name="est_publie" class="confirmedelete" value='.$post['est_publie'].'></td>';

}


$result['resultat'] .= '<td class="member_action">';
$result['resultat'] .= '<a href='.$post['url'].' class="linkbtn"></a>';
$result['resultat'] .= '<input type="button" class="viewbtn" name="view" id="'.$post['id_post'].'"></input>';
$result['resultat'] .= '<input type="button" class="editbtn" id="'.$post['id_post'].'"></input>';
$result['resultat'] .= '<input type="button" class="deletebtn"></input>';
$result['resultat'] .= '</td>';

}else{

$result['resultat'] .= '<td class="member_action">';
$result['resultat'] .= '<a href='.$post['url'].' class="linkbtn"></a>';
$result['resultat'] .= '</td>';

}

$result['resultat'] .= '</tr>';


}

$result['resultat'] .= '</tbody>';

$result['resultat'] .= '</table>';
      

  
  }
  
// retour Ajax
  echo json_encode($result);
  }

?>