import React, { useState } from 'react';
import api from '../../services/api'; 
import './PhotoForm.css'; 

const PhotoForm = ({ photo, onSuccess, onCancel }) => {
  // State for form fields
  const [title, setTitle] = useState(photo ? photo.title : '');
  const [description, setDescription] = useState(photo ? photo.description : '');
  const [tags, setTags] = useState(photo ? photo.tags : '');
  const [file, setFile] = useState(null);
  const [error, setError] = useState('');

  // Handle file selection
  const handleFileChange = (e) => {
    setFile(e.target.files[0] || null);
  };

  // Handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('tags', tags);

      // Get user ID from local storage
      const userId = localStorage.getItem('user_id');
      if (!userId) {
        setError("User not authenticated.");
        return;
      }
      formData.append('user_id', userId);

      // Append image file if selected
      if (file) {
        formData.append('image', file);
      }

      // If editing an existing photo
      if (photo && photo.id) {
        formData.append('id', photo.id);
        const response = await api.post('/index.php?api=photo&action=update', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (response.data.success) {
          onSuccess();
        } else {
          setError(response.data.message);
        }
      } else {
        // If adding a new photo
        const response = await api.post('/index.php?api=photo&action=create', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (response.data.success) {
          onSuccess();
        } else {
          setError(response.data.message);
        }
      }
    } catch (err) {
      console.error("Error saving photo", err);
      setError("Error saving photo.");
    }
  };

  return (
    <div className="photo-form-container">
      <h2>{photo && photo.id ? "Edit Photo" : "Add New Photo"}</h2>
      {error && <p className="error-msg">{error}</p>} 
      <form onSubmit={handleSubmit} className="photo-form">
        <input
          type="text"
          placeholder="Title"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          required
        />
        <textarea
          placeholder="Description"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
        />
        <input
          type="text"
          placeholder="Tags"
          value={tags}
          onChange={(e) => setTags(e.target.value)}
        />
        <input
          type="file"
          accept="image/*"
          onChange={handleFileChange}
        />

        <div className="photo-form-buttons">
          <button type="submit" className="save-button">Save</button> 
          <button type="button" className="cancel-button" onClick={onCancel}>Cancel</button> 
        </div>
      </form>
    </div>
  );
};

export default PhotoForm;
