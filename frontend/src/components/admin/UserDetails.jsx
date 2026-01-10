import { useState, useEffect } from 'react';

const UserDetails = ({ userId, onClose }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadUserDetails();
  }, [userId]);

  const loadUserDetails = async () => {
    try {
      const response = await fetch(`http://localhost:8000/api/users/${userId}`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      const data = await response.json();
      setUser(data);
    } catch (err) {
      console.error('Erreur lors du chargement des détails');
    } finally {
      setLoading(false);
    }
  };

  if (loading) return <div className="text-center py-4">Chargement...</div>;
  if (!user) return <div className="text-center py-4">Utilisateur non trouvé</div>;

  return (
    <div className="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4">
      <div className="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-96 overflow-y-auto">
        <div className="px-6 py-4 border-b border-gray-200">
          <div className="flex justify-between items-center">
            <h3 className="text-lg font-medium text-gray-900">
              Détails de l'utilisateur
            </h3>
            <button
              onClick={onClose}
              className="text-gray-400 hover:text-gray-600"
            >
              ✕
            </button>
          </div>
        </div>
        
        <div className="px-6 py-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700">Nom</label>
              <p className="mt-1 text-sm text-gray-900">{user.nom}</p>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Prénom</label>
              <p className="mt-1 text-sm text-gray-900">{user.prenom}</p>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Email</label>
              <p className="mt-1 text-sm text-gray-900">{user.email}</p>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Rôle</label>
              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                user.role === 'ETUDIANT' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
              }`}>
                {user.role}
              </span>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Matricule</label>
              <p className="mt-1 text-sm text-gray-900">{user.matricule}</p>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700">Filière</label>
              <p className="mt-1 text-sm text-gray-900">{user.filiere?.nom_filiere}</p>
            </div>
            
            {user.telephone && (
              <div>
                <label className="block text-sm font-medium text-gray-700">Téléphone</label>
                <p className="mt-1 text-sm text-gray-900">{user.telephone}</p>
              </div>
            )}
            
            {user.ine && (
              <div>
                <label className="block text-sm font-medium text-gray-700">INE</label>
                <p className="mt-1 text-sm text-gray-900">{user.ine}</p>
              </div>
            )}
            
            {user.niveau && (
              <div>
                <label className="block text-sm font-medium text-gray-700">Niveau</label>
                <p className="mt-1 text-sm text-gray-900">{user.niveau}</p>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default UserDetails;