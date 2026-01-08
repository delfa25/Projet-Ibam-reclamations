import { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';
import CreateReclamationForm from '../components/etudiant/CreateReclamationForm';
import ReclamationsList from '../components/etudiant/ReclamationsList';

const ReclamationsPage = () => {
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [selectedReclamation, setSelectedReclamation] = useState(null);
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const handleCreateSuccess = () => {
    setShowCreateForm(false);
    // La liste se rechargera automatiquement
  };

  const handleView = (reclamation) => {
    setSelectedReclamation(reclamation);
  };

  const handleEdit = (reclamation) => {
    // TODO: Implémenter l'édition
    alert('Fonctionnalité d\'édition à venir');
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-6">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                IBAM - Réclamations
              </h1>
              <p className="text-sm text-gray-600">
                Espace Étudiant - {user.name}
              </p>
            </div>
            <div className="flex items-center space-x-4">
              <button
                onClick={() => navigate('/dashboard')}
                className="text-gray-600 hover:text-gray-900"
              >
                Tableau de bord
              </button>
              <button
                onClick={handleLogout}
                className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
              >
                Déconnexion
              </button>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div className="px-4 py-6 sm:px-0">
          {showCreateForm ? (
            <CreateReclamationForm
              onSuccess={handleCreateSuccess}
              onCancel={() => setShowCreateForm(false)}
            />
          ) : selectedReclamation ? (
            <div className="bg-white p-6 rounded-lg shadow">
              <div className="flex justify-between items-center mb-6">
                <h2 className="text-2xl font-bold text-gray-900">
                  Détails de la réclamation
                </h2>
                <button
                  onClick={() => setSelectedReclamation(null)}
                  className="text-gray-600 hover:text-gray-900"
                >
                  ← Retour à la liste
                </button>
              </div>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h3 className="font-medium text-gray-900">N° Demande</h3>
                  <p className="text-gray-600">{selectedReclamation.numero_demande}</p>
                </div>
                <div>
                  <h3 className="font-medium text-gray-900">Matière</h3>
                  <p className="text-gray-600">{selectedReclamation.matiere?.nom_matiere}</p>
                </div>
                <div>
                  <h3 className="font-medium text-gray-900">Statut</h3>
                  <p className="text-gray-600">{selectedReclamation.statut}</p>
                </div>
                <div>
                  <h3 className="font-medium text-gray-900">Date de création</h3>
                  <p className="text-gray-600">{selectedReclamation.created_at}</p>
                </div>
                <div className="md:col-span-2">
                  <h3 className="font-medium text-gray-900">Objet</h3>
                  <p className="text-gray-600">{selectedReclamation.objet_demande}</p>
                </div>
                <div className="md:col-span-2">
                  <h3 className="font-medium text-gray-900">Motif</h3>
                  <p className="text-gray-600">{selectedReclamation.motif}</p>
                </div>
              </div>
            </div>
          ) : (
            <div className="space-y-6">
              <div className="flex justify-between items-center">
                <h2 className="text-2xl font-bold text-gray-900">
                  Mes Réclamations
                </h2>
                <button
                  onClick={() => setShowCreateForm(true)}
                  className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium"
                >
                  + Nouvelle Réclamation
                </button>
              </div>
              
              <ReclamationsList
                onView={handleView}
                onEdit={handleEdit}
              />
            </div>
          )}
        </div>
      </main>
    </div>
  );
};

export default ReclamationsPage;