const ModernCard = ({ 
  title, 
  subtitle, 
  value, 
  icon, 
  color = 'blue', 
  onClick, 
  className = '',
  children 
}) => {
  const colorClasses = {
    blue: 'from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700',
    green: 'from-green-500 to-green-600 hover:from-green-600 hover:to-green-700',
    orange: 'from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700',
    purple: 'from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700',
    indigo: 'from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700',
    red: 'from-red-500 to-red-600 hover:from-red-600 hover:to-red-700',
    gray: 'from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700'
  };

  const iconBgClasses = {
    blue: 'bg-blue-100 text-blue-600',
    green: 'bg-green-100 text-green-600',
    orange: 'bg-orange-100 text-orange-600',
    purple: 'bg-purple-100 text-purple-600',
    indigo: 'bg-indigo-100 text-indigo-600',
    red: 'bg-red-100 text-red-600',
    gray: 'bg-gray-100 text-gray-600'
  };

  const CardContent = () => (
    <div className={`bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 ${onClick ? 'cursor-pointer' : ''} ${className}`}>
      <div className="flex items-start justify-between">
        <div className="flex-1">
          {icon && (
            <div className={`inline-flex p-3 rounded-xl ${iconBgClasses[color]} mb-4`}>
              {icon}
            </div>
          )}
          
          {title && (
            <h3 className="text-lg font-semibold text-gray-900 mb-1">
              {title}
            </h3>
          )}
          
          {subtitle && (
            <p className="text-gray-600 text-sm mb-3">
              {subtitle}
            </p>
          )}
          
          {value && (
            <div className="text-3xl font-bold text-gray-900 mb-2">
              {value}
            </div>
          )}
          
          {children}
        </div>
      </div>
      
      {onClick && (
        <div className="mt-4 pt-4 border-t border-gray-100">
          <div className={`inline-flex items-center px-4 py-2 bg-gradient-to-r ${colorClasses[color]} text-white rounded-xl text-sm font-medium transition-all duration-200 hover:shadow-lg`}>
            Voir plus
            <svg className="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
          </div>
        </div>
      )}
    </div>
  );

  return onClick ? (
    <div onClick={onClick}>
      <CardContent />
    </div>
  ) : (
    <CardContent />
  );
};

export default ModernCard;