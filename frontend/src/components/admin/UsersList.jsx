import { useState, useEffect } from 'react';
import CreateUserForm from './CreateUserForm';
import UserDetails from './UserDetails';

const UsersList = () => {
  const [users, setUsers] = useState([]);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [selectedUserId, setSelectedUserId] = useState(null);
  const [filter, setFilter] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadUsers();
  }, [filter]);

  const loadUsers = async () => {
    setLoading(true);
    try {
      const url = filter ? `http://localhost:8000/api/users?role=${filter}` : 'http://localhost:8000/api/users';
      const response = await fetch(url, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      const data = await response.json();
      setUsers(data);
    } catch (err) {
      console.error('Erreur lors du chargement des utilisateurs');
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (userId) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
      try {
        await fetch(`http://localhost:8000/api/users/${userId}`, {
          method: 'DELETE',
          headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
        });
        loadUsers();
      } catch (err) {
        console.error('Erreur lors de la suppression');
      }
    }
  };

  if (selectedUserId) {
    return (
      <UserDetails
        userId={selectedUserId}
        onClose={() => setSelectedUserId(null)}
      />
    );
  }

  if (showCreateForm) {
    return (
      <CreateUserForm
        onSuccess={() => {
          setShowCreateForm(false);
          loadUsers();
        }}
        onCancel={() => setShowCreateForm(false)}
      />
    );
  }

  return (
    <div className="p-6">
      <div className="flex justify-between items-center mb-6">
        <h1 className="text-2xl font-bold text-gray-900">Gestion des Utilisateurs</h1>
        <button
          onClick={() => setShowCreateForm(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Créer un utilisateur
        </button>
      </div>

      <div className="mb-4">
        <select
          value={filter}
          onChange={(e) => setFilter(e.target.value)}
          className="px-3 py-2 border border-gray-300 rounded-md"
        >
          <option value="">Tous les utilisateurs</option>
          <option value="ETUDIANT">Étudiants</option>
          <option value="ENSEIGNANT">Enseignants</option>
        </select>
      </div>

      {loading ? (
        <div className="text-center py-4">Chargement...</div>
      ) : (
        <div className="bg-white shadow overflow-hidden sm:rounded-md">
          <ul className="divide-y divide-gray-200">
            {users.map((user) => (
              <li key={user.id} className="px-6 py-4">
                <div className="flex items-center justify-between">
                  <div className="flex-1">
                    <div className="flex items-center">
                      <div className="flex-shrink-0">
                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                          user.role === 'ETUDIANT' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                        }`}>
                          {user.role}
                        </span>
                      </div>
                      <div className="ml-4">
                        <div className="text-sm font-medium text-gray-900">
                          {user.prenom} {user.nom}
                        </div>
                        <div className="text-sm text-gray-500">
                          {user.email} • {user.matricule}
                        </div>
                        {user.ine && (
                          <div className="text-sm text-gray-500">
                            INE: {user.ine} • Niveau: {user.niveau}
                          </div>
                        )}
                        <div className="text-sm text-gray-500">
                          Filière: {user.filiere?.nom_filiere}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="flex space-x-2">
                    <button
                      onClick={() => setSelectedUserId(user.id)}
                      className="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                    >
                      Voir détails
                    </button>
                    <button
                      onClick={() => handleDelete(user.id)}
                      className="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
};

export default UsersList;