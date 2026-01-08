import { useState, useEffect } from 'react';
import { reclamationService } from '../../services/reclamationService';

const CreateReclamationForm = ({ onSuccess, onCancel }) => {
  const [formData, setFormData] = useState({
    matiere_id: '',
    objet_demande: '',
    motif: ''
  });
  const [matieres, setMatieres] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    loadMatieres();
  }, []);

  const loadMatieres = async () => {
    try {
      const data = await reclamationService.getMatieres();
      setMatieres(data);
    } catch (err) {
      setError('Erreur lors du chargement des matières');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      await reclamationService.create(formData);
      onSuccess?.();
    } catch (err) {
      setError(err.response?.data?.message || 'Erreur lors de la création');
    } finally {
      setLoading(false);
    }
  };

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  return (
    <div className="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
      <h2 className="text-2xl font-bold text-gray-900 mb-6">
        Nouvelle Réclamation
      </h2>

      {error && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Matière *
          </label>
          <select
            name="matiere_id"
            value={formData.matiere_id}
            onChange={handleChange}
            required
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">Sélectionner une matière</option>
            {matieres.map(matiere => (
              <option key={matiere.id} value={matiere.id}>
                {matiere.nom_matiere} ({matiere.code_matiere})
              </option>
            ))}
          </select>
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Objet de la demande *
          </label>
          <input
            type="text"
            name="objet_demande"
            value={formData.objet_demande}
            onChange={handleChange}
            required
            placeholder="Ex: Erreur de note, Note manquante..."
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Motif détaillé *
          </label>
          <textarea
            name="motif"
            value={formData.motif}
            onChange={handleChange}
            required
            rows={4}
            placeholder="Expliquez en détail votre réclamation..."
            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div className="flex justify-end space-x-4">
          <button
            type="button"
            onClick={onCancel}
            className="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300"
          >
            Annuler
          </button>
          <button
            type="submit"
            disabled={loading}
            className="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
          >
            {loading ? 'Création...' : 'Créer (Brouillon)'}
          </button>
        </div>
      </form>
    </div>
  );
};

export default CreateReclamationForm;