<?php
// Vérifier si l'utilisateur est connecté en tant qu'administrateur
session_start();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
  header("Location: login.php");
  exit;
}

// Connexion à la base de données
$servername = "localhost";
$username = "nom_utilisateur";
$password = "mot_de_passe";
$dbname = "nom_base_de_donnees";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Erreur de connexion à la base de données : " . $conn->connect_error);
}

// Fonction pour valider la publication d'une annonce
function validerAnnonce($annonceId) {
  global $conn;
  
  $sql = "UPDATE annonces SET valide = 1 WHERE id = $annonceId";
  
  if ($conn->query($sql) === TRUE) {
    echo "L'annonce a été validée avec succès.";
  } else {
    echo "Erreur lors de la validation de l'annonce : " . $conn->error;
  }
}

// Fonction pour supprimer une annonce
function supprimerAnnonce($annonceId) {
  global $conn;
  
  $sql = "DELETE FROM annonces WHERE id = $annonceId";
  
  if ($conn->query($sql) === TRUE) {
    echo "L'annonce a été supprimée avec succès.";
  } else {
    echo "Erreur lors de la suppression de l'annonce : " . $conn->error;
  }
}

// Fonction pour supprimer un compte client
function supprimerCompteClient($clientId) {
  global $conn;
  
  $sql = "DELETE FROM clients WHERE id = $clientId";
  
  if ($conn->query($sql) === TRUE) {
    echo "Le compte client a été supprimé avec succès.";
  } else {
    echo "Erreur lors de la suppression du compte client : " . $conn->error;
  }
}

// Vérifier si une action a été soumise (validation ou suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $id = $_POST['id'];
    
    if ($action === 'valider') {
      validerAnnonce($id);
    } elseif ($action === 'supprimer_annonce') {
      supprimerAnnonce($id);
    } elseif ($action === 'supprimer_compte_client') {
      supprimerCompteClient($id);
    }
  }
}

// Récupérer les annonces en attente de validation
$sql = "SELECT * FROM annonces WHERE valide = 0";
$result = $conn->query($sql);

// Récupérer les comptes clients
$sql_clients = "SELECT * FROM clients";
$result_clients = $conn->query($sql_clients);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Back-office - Gestion des annonces et des comptes clients</title>
</head>
<body>
  <h1>Back-office - Gestion des annonces et des comptes clients</h1>
  
  <h2>Annonces en attente de validation</h2>
  <table>
    <tr>
      <th>Titre</th>
      <th>Description</th>
      <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['titre'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='action' value='valider'>";
        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
        echo "<button type='submit'>Valider</button>";
        echo "</form>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='action' value='supprimer_annonce'>";
        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
        echo "<button type='submit'>Supprimer</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='3'>Aucune annonce en attente de validation.</td></tr>";
    }
    ?>
  </table>
  
  <h2>Comptesclients</h2>
  <table>
    <tr>
      <th>Nom</th>
      <th>Email</th>
      <th>Actions</th>
    </tr>
    <?php
    if ($result_clients->num_rows > 0) {
      while ($row = $result_clients->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['nom'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>";
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='action' value='supprimer_compte_client'>";
        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
        echo "<button type='submit'>Supprimer</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
      }
    } else {
      echo "<tr><td colspan='3'>Aucun compte client trouvé.</td></tr>";
    }
    ?>
  </table>
</body>
</html>