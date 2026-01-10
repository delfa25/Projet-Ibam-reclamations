import { useState } from 'react';
import UsersList from './UsersList';

const AdminDashboard = () => {
  const [activeTab, setActiveTab] = useState('users');

  const tabs = [
    { id: 'users', label: 'Gestion des Utilisateurs', icon: '👥' },
    { id: 'reclamations', label: 'Réclamations', icon: '📋' },
    { id: 'stats', label: 'Statistiques', icon: '📊' }
  ];

  const renderContent = () => {
    switch (activeTab) {
      case 'users':
        return <UsersList />;
      case 'reclamations':
        return <div className="p-6">Gestion des réclamations (à implémenter)</div>;
      case 'stats':
        return <div className="p-6">Statistiques (à implémenter)</div>;
      default:
        return <UsersList />;
    }
  };

  return (
    <div className="min-h-screen bg-gray-100">
      <nav className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex justify-between h-16">
            <div className="flex items-center">
              <h1 className="text-xl font-semibold text-gray-900">
                Administration - Directeur Adjoint
              </h1>
            </div>
            <div className="flex items-center space-x-4">
              <button className="text-gray-500 hover:text-gray-700">
                Déconnexion
              </button>
            </div>
          </div>
        </div>
      </nav>

      <div className="max-w-7xl mx-auto py-6">
        <div className="flex">
          <div className="w-64 bg-white shadow rounded-lg mr-6">
            <div className="p-4">
              <h2 className="text-lg font-medium text-gray-900 mb-4">Menu</h2>
              <nav className="space-y-2">
                {tabs.map((tab) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`w-full flex items-center px-3 py-2 text-sm font-medium rounded-md ${
                      activeTab === tab.id
                        ? 'bg-blue-100 text-blue-700'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                    }`}
                  >
                    <span className="mr-3">{tab.icon}</span>
                    {tab.label}
                  </button>
                ))}
              </nav>
            </div>
          </div>

          <div className="flex-1 bg-white shadow rounded-lg">
            {renderContent()}
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;