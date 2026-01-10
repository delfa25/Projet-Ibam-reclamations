import api from './api';

export const reclamationService = {
  async getAll() {
    const response = await api.get('/reclamations');
    return response.data;
  },

  async getById(id) {
    const response = await api.get(`/reclamations/${id}`);
    return response.data;
  },

  async create(data) {
    const response = await api.post('/reclamations', data);
    return response.data;
  },

  async update(id, data) {
    const response = await api.put(`/reclamations/${id}`, data);
    return response.data;
  },

  async delete(id) {
    const response = await api.delete(`/reclamations/${id}`);
    return response.data;
  },

  async soumettre(id) {
    const response = await api.post(`/reclamations/${id}/soumettre`);
    return response.data;
  },

  async verifier(id, data) {
    const response = await api.put(`/reclamations/${id}/verifier`, data);
    return response.data;
  },

  async imputer(id) {
    const response = await api.put(`/reclamations/${id}/imputer`);
    return response.data;
  },

  async traiter(id, data) {
    const response = await api.put(`/reclamations/${id}/traiter`, data);
    return response.data;
  },

  async getMatieres() {
    const response = await api.get('/matieres');
    return response.data;
  },

  async getFilieres() {
    const response = await api.get('/filieres');
    return response.data;
  },

  async getMatieresByFiliere(filiereId) {
    const response = await api.get(`/filieres/${filiereId}/matieres`);
    return response.data;
  },

  async getEnseignantsByFiliere(filiereId) {
    const response = await api.get(`/filieres/${filiereId}/enseignants`);
    return response.data;
  }
};