import { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';
import { reclamationService } from '../services/reclamationService';
import ReclamationDetails from '../components/ReclamationDetails';
import TransmettreScolariteForm from '../components/admin/TransmettreScolariteForm';
import ImputerForm from '../components/admin/ImputerForm';
import VerifyReclamationForm from '../components/scolarite/VerifyReclamationForm';
import TraiterReclamationForm from '../components/enseignant/TraiterReclamationForm';
import FinaliserReclamationForm from '../components/scolarite/FinaliserReclamationForm';
import StatusBadge from '../components/common/StatusBadge';

const DAPage = () => {
  const [reclamations, setReclamations] = useState([]);
  const [selectedReclamation, setSelectedReclamation] = useState(null);
  const [viewingDetails, setViewingDetails] = useState(false);
  const [transmettingToScolarite, setTransmettingToScolarite] = useState(false);
  const [imputing, setImputing] = useState(false);
  const [verifying, setVerifying] = useState(false);
  const [treating, setTreating] = useState(false);
  const [finalizing, setFinalizing] = useState(false);
  const [filter, setFilter] = useState('ALL');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  useEffect(() => {
    loadReclamations();
  }, []);

  const loadReclamations = async () => {
    try {
      const data = await reclamationService.getAll();
      // DA voit TOUTES les réclamations (super-admin)
      const visibleStatuses = ['SOUMIS', 'RECEVABLE', 'REJETE', 'EN_TRAITEMENT', 'VALIDE_ENSEIGNANT', 'INVALIDE_ENSEIGNANT', 'TRANSMIS_SCOLARITE', 'TRAITE'];
      setReclamations(data.filter(r => visibleStatuses.includes(r.status)));
    } catch (err) {
      setError('Erreur lors du chargement');
    } finally {
      setLoading(false);
    }
  };

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const getFilteredReclamations = () => {
    if (filter === 'ALL') return reclamations;
    if (filter === 'A_VERIFIER') return reclamations.filter(r => r.status === 'SOUMIS');
    if (filter === 'A_IMPUTER') return reclamations.filter(r => r.status === 'RECEVABLE');
    if (filter === 'EN_COURS') return reclamations.filter(r => r.status === 'EN_TRAITEMENT');
    if (filter === 'A_TRANSMETTRE') return reclamations.filter(r => ['VALIDE_ENSEIGNANT', 'INVALIDE_ENSEIGNANT'].includes(r.status));
    if (filter === 'A_FINALISER') return reclamations.filter(r => r.status === 'TRANSMIS_SCOLARITE');
    return reclamations.filter(r => r.status === filter);
  };

  if (loading) {
    return <div className="min-h-screen flex items-center justify-center">Chargement...</div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Administration des réclamations</h1>
          <p className="text-sm text-gray-500">Bienvenue, {user.name}</p>
        </div>
      </div>

      <div className="bg-white shadow rounded-lg p-6">
        {selectedReclamation && viewingDetails ? (
          <ReclamationDetails
            reclamationId={selectedReclamation.id}
            onClose={() => {
              setSelectedReclamation(null);
              setViewingDetails(false);
            }}
          />
        ) : selectedReclamation && verifying ? (
          <VerifyReclamationForm
            reclamation={selectedReclamation}
            onSuccess={() => {
              setSelectedReclamation(null);
              setVerifying(false);
              loadReclamations();
            }}
            onCancel={() => {
              setSelectedReclamation(null);
              setVerifying(false);
            }}
          />
        ) : selectedReclamation && imputing ? (
          <ImputerForm
            reclamation={selectedReclamation}
            onSuccess={() => {
              setSelectedReclamation(null);
              setImputing(false);
              loadReclamations();
            }}
            onCancel={() => {
              setSelectedReclamation(null);
              setImputing(false);
            }}
          />
        ) : selectedReclamation && treating ? (
          <TraiterReclamationForm
            reclamation={selectedReclamation}
            onSuccess={() => {
              setSelectedReclamation(null);
              setTreating(false);
              loadReclamations();
            }}
            onCancel={() => {
              setSelectedReclamation(null);
              setTreating(false);
            }}
          />
        ) : selectedReclamation && transmettingToScolarite ? (
          <TransmettreScolariteForm
            reclamation={selectedReclamation}
            onSuccess={() => {
              setSelectedReclamation(null);
              setTransmettingToScolarite(false);
              loadReclamations();
            }}
            onCancel={() => {
              setSelectedReclamation(null);
              setTransmettingToScolarite(false);
            }}
          />
        ) : selectedReclamation && finalizing ? (
          <FinaliserReclamationForm
            reclamation={selectedReclamation}
            onSuccess={() => {
              setSelectedReclamation(null);
              setFinalizing(false);
              loadReclamations();
            }}
            onCancel={() => {
              setSelectedReclamation(null);
              setFinalizing(false);
            }}
          />
        ) : (
          <div className="space-y-6">
            <div className="flex justify-between items-center">
              <h2 className="text-xl font-semibold text-gray-900">
                Toutes les réclamations ({reclamations.length})
              </h2>
              <div className="flex space-x-2">
                <select
                  value={filter}
                  onChange={(e) => setFilter(e.target.value)}
                  className="px-3 py-1 border border-gray-300 rounded text-sm focus:ring-blue-500 focus:border-blue-500"
                >
                  <option value="ALL">Toutes</option>
                  <option value="A_VERIFIER">À vérifier</option>
                  <option value="A_IMPUTER">À imputer</option>
                  <option value="EN_COURS">En traitement</option>
                  <option value="A_TRANSMETTRE">À transmettre</option>
                  <option value="A_FINALISER">À finaliser</option>
                </select>
              </div>
            </div>

            {error && (
              <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                {error}
              </div>
            )}

            <div className="overflow-hidden border border-gray-200 rounded-lg">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      N° Demande
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Étudiant
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Matière
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Statut
                    </th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Date
                    </th>
                    <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {getFilteredReclamations().map((reclamation) => (
                    <tr key={reclamation.id} className="hover:bg-gray-50 transition-colors">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {reclamation.numero_demande}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {reclamation.etudiant?.name}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {reclamation.matiere?.nom_matiere}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <StatusBadge status={reclamation.status} />
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(reclamation.created_at).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                        <button
                          onClick={() => {
                            setSelectedReclamation(reclamation);
                            setViewingDetails(true);
                          }}
                          className="text-blue-600 hover:text-blue-900"
                        >
                          Détails
                        </button>
                        {reclamation.status === 'SOUMIS' && (
                          <button
                            onClick={() => {
                              setSelectedReclamation(reclamation);
                              setVerifying(true);
                            }}
                            className="text-indigo-600 hover:text-indigo-900 font-semibold"
                          >
                            Vérifier
                          </button>
                        )}
                        {reclamation.status === 'RECEVABLE' && (
                          <button
                            onClick={() => {
                              setSelectedReclamation(reclamation);
                              setImputing(true);
                            }}
                            className="text-green-600 hover:text-green-900 font-semibold"
                          >
                            Imputer
                          </button>
                        )}
                        {reclamation.status === 'EN_TRAITEMENT' && (
                          <button
                            onClick={() => {
                              setSelectedReclamation(reclamation);
                              setTreating(true);
                            }}
                            className="text-orange-600 hover:text-orange-900 font-semibold"
                          >
                            Traiter
                          </button>
                        )}
                        {(reclamation.status === 'VALIDE_ENSEIGNANT' || reclamation.status === 'INVALIDE_ENSEIGNANT') && (
                          <button
                            onClick={() => {
                              setSelectedReclamation(reclamation);
                              setTransmettingToScolarite(true);
                            }}
                            className="text-purple-600 hover:text-purple-900 font-semibold"
                          >
                            Transmettre
                          </button>
                        )}
                        {reclamation.status === 'TRANSMIS_SCOLARITE' && (
                          <button
                            onClick={() => {
                              setSelectedReclamation(reclamation);
                              setFinalizing(true);
                            }}
                            className="text-teal-600 hover:text-teal-900 font-semibold"
                          >
                            Finaliser
                          </button>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default DAPage;