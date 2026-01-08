import { useAuth } from '../contexts/AuthContext';
import { useNavigate } from 'react-router-dom';

const ModernHeader = ({ title, subtitle, showBackButton = false }) => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const getRoleColor = (role) => {
    const colors = {
      'ETUDIANT': 'bg-blue-100 text-blue-800',
      'SCOLARITE': 'bg-orange-100 text-orange-800',
      'ENSEIGNANT': 'bg-purple-100 text-purple-800',
      'DA': 'bg-indigo-100 text-indigo-800'
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
  };

  const getRoleDisplay = (role) => {
    const roles = {
      'ETUDIANT': 'Étudiant',
      'SCOLARITE': 'Agent Scolarité',
      'ENSEIGNANT': 'Enseignant',
      'DA': 'Directeur Adjoint'
    };
    return roles[role] || role;
  };

  return (
    <header className=\"bg-white border-b border-gray-100 sticky top-0 z-50 backdrop-blur-sm bg-white/95\">\n      <div className=\"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8\">\n        <div className=\"flex justify-between items-center py-4\">\n          <div className=\"flex items-center space-x-4\">\n            {showBackButton && (\n              <button\n                onClick={() => navigate('/dashboard')}\n                className=\"p-2 hover:bg-gray-100 rounded-xl transition-colors duration-200\"\n              >\n                <svg className=\"w-5 h-5 text-gray-600\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                  <path strokeLinecap=\"round\" strokeLinejoin=\"round\" strokeWidth={2} d=\"M15 19l-7-7 7-7\" />\n                </svg>\n              </button>\n            )}\n            <div>\n              <div className=\"flex items-center space-x-3\">\n                <div className=\"h-10 w-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center\">\n                  <span className=\"text-white font-bold text-sm\">IBAM</span>\n                </div>\n                <div>\n                  <h1 className=\"text-2xl font-bold text-gray-900\">{title}</h1>\n                  {subtitle && <p className=\"text-sm text-gray-600\">{subtitle}</p>}\n                </div>\n              </div>\n            </div>\n          </div>\n          \n          <div className=\"flex items-center space-x-4\">\n            <div className=\"hidden sm:flex items-center space-x-3\">\n              <div className=\"text-right\">\n                <p className=\"text-sm font-medium text-gray-900\">{user.name}</p>\n                <span className={`inline-flex px-2 py-1 text-xs font-medium rounded-full ${getRoleColor(user.role)}`}>\n                  {getRoleDisplay(user.role)}\n                </span>\n              </div>\n            </div>\n            \n            <div className=\"flex items-center space-x-2\">\n              <button\n                onClick={() => navigate('/dashboard')}\n                className=\"p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-all duration-200\"\n                title=\"Tableau de bord\"\n              >\n                <svg className=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                  <path strokeLinecap=\"round\" strokeLinejoin=\"round\" strokeWidth={2} d=\"M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z\" />\n                  <path strokeLinecap=\"round\" strokeLinejoin=\"round\" strokeWidth={2} d=\"M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z\" />\n                </svg>\n              </button>\n              \n              <button\n                onClick={handleLogout}\n                className=\"bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 hover:shadow-md\"\n              >\n                Déconnexion\n              </button>\n            </div>\n          </div>\n        </div>\n      </div>\n    </header>\n  );\n};\n\nexport default ModernHeader;