# 📡 API DOCUMENTATION - IBAM

## 🔑 Authentification

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "etudiant@ibam.bf",
  "password": "password"
}
```

**Réponse :**
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "Étudiant Test",
    "email": "etudiant@ibam.bf",
    "role": "ETUDIANT"
  }
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

## 📝 Réclamations

### Créer une réclamation
```http
POST /api/reclamations
Authorization: Bearer {token}
Content-Type: application/json

{
  "matiere_id": 1,
  "objet_demande": "Erreur de note",
  "motif": "Ma note devrait être 15/20"
}
```

### Lister les réclamations
```http
GET /api/reclamations
Authorization: Bearer {token}
```

### Soumettre une réclamation
```http
POST /api/reclamations/{id}/soumettre
Authorization: Bearer {token}
```

## 📄 Justificatifs

### Upload justificatif
```http
POST /api/reclamations/{id}/justificatifs
Authorization: Bearer {token}
Content-Type: multipart/form-data

fichier: [FILE]
```

## 🔔 Notifications

### Lister notifications
```http
GET /api/notifications
Authorization: Bearer {token}
```

## 📊 Codes de Réponse

- `200` : Succès
- `201` : Créé
- `400` : Erreur de validation
- `401` : Non authentifié
- `403` : Non autorisé
- `404` : Non trouvé
- `500` : Erreur serveur