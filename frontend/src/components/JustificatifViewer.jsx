import { useState } from 'react';

const JustificatifViewer = ({ justificatifs }) => {
  const [downloading, setDownloading] = useState(null);

  const handleDownload = async (justificatif) => {
    setDownloading(justificatif.id);
    try {
      const response = await fetch(`http://localhost:8000/api/justificatifs/${justificatif.id}/download`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      });
      
      if (response.ok) {
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = justificatif.nom_fichier;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      }
    } catch (error) {
      console.error('Erreur téléchargement:', error);
    } finally {
      setDownloading(null);
    }
  };

  const getFileIcon = (fileName) => {
    const ext = fileName.split('.').pop().toLowerCase();
    if (['jpg', 'jpeg', 'png'].includes(ext)) return '🖼️';
    if (ext === 'pdf') return '📄';
    return '📎';
  };

  if (!justificatifs || justificatifs.length === 0) {
    return (
      <div className="text-gray-500 text-sm">
        Aucun justificatif joint
      </div>
    );
  }

  return (
    <div className="space-y-2">
      <h4 className="font-medium text-gray-900">Justificatifs joints :</h4>
      {justificatifs.map((justificatif) => (
        <div key={justificatif.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-md">
          <div className="flex items-center space-x-3">
            <span className="text-2xl">{getFileIcon(justificatif.nom_fichier)}</span>
            <div>
              <p className="text-sm font-medium text-gray-900">{justificatif.nom_fichier}</p>
              <p className="text-xs text-gray-500">
                {(justificatif.taille / 1024).toFixed(1)} KB
              </p>
            </div>
          </div>
          <button
            onClick={() => handleDownload(justificatif)}
            disabled={downloading === justificatif.id}
            className="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 disabled:opacity-50"
          >
            {downloading === justificatif.id ? 'Téléchargement...' : 'Télécharger'}
          </button>
        </div>
      ))}
    </div>
  );
};

export default JustificatifViewer;