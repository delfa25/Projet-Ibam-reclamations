<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réclamation Non Recevable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #dc2626;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Réclamation Non Recevable</h1>
    </div>
    
    <div class="content">
        <p>Bonjour {{ $reclamation->etudiant->nom }} {{ $reclamation->etudiant->prenom }},</p>
        
        <p>Nous vous informons que votre réclamation concernant <strong>{{ $reclamation->objet }}</strong> a été examinée par le service de scolarité.</p>
        
        <div class="info-box">
            <p><strong>Statut :</strong> Non recevable</p>
            <p><strong>Numéro de demande :</strong> {{ $reclamation->numero_demande ?? 'N/A' }}</p>
            <p><strong>Matière :</strong> {{ $reclamation->matiere->nom_matiere ?? 'N/A' }}</p>
            <p><strong>Type :</strong> {{ $reclamation->type }}</p>
        </div>
        
        <p>Malheureusement, votre réclamation ne peut pas être traitée pour les raisons suivantes :</p>
        <ul>
            <li>La demande ne respecte pas les critères de recevabilité</li>
            <li>Les justificatifs fournis sont insuffisants ou non conformes</li>
            <li>Le délai de réclamation a été dépassé</li>
        </ul>
        
        <p>Si vous pensez qu'il s'agit d'une erreur ou si vous souhaitez obtenir plus d'informations, nous vous invitons à contacter le service de scolarité.</p>
        
        <p>Cordialement,<br>
        <strong>Le Service de Scolarité - IBAM</strong></p>
    </div>
    
    <div class="footer">
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        <p>&copy; {{ date('Y') }} Institut Burkinabé des Arts et Métiers (IBAM)</p>
    </div>
</body>
</html>
