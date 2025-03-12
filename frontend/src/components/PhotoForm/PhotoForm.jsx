import React, { useState } from 'react';
import api from '../../services/api';
import './PhotoForm.css';

const PhotoForm = ({ photo, onSuccess, onCancel }) => {
  // We still keep title, description, tags in state.
  const [title, setTitle] = useState(photo ? photo.title : '');
  const [description, setDescription] = useState(photo ? photo.description : '');
  const [tags, setTags] = useState(photo ? photo.tags : '');
  // We remove image_path state and replace it with a file state:
  const [file, setFile] = useState(null);

  const [error, setError] = useState('');

  const handleFileChange = (e) => {
    // Store the selected file in state
    setFile(e.target.files[0] || null);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      // We’ll build a FormData object to handle both text fields and the file
      const formData = new FormData();
      formData.append('title', title);
      formData.append('description', description);
      formData.append('tags', tags);
      // For demonstration, we use user_id=1 or retrieve from your auth context
      formData.append('user_id', 1);

      // If the user selected a file, append it under the name 'image'
      if (file) {
        formData.append('image', file);
      }

      // If we're editing an existing photo, append the 'id' field
      if (photo && photo.id) {
        formData.append('id', photo.id);

        // Make a POST request to update
        const response = await api.post('/index.php?api=photo&action=update', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        if (response.data.success) {
          onSuccess();
        } else {
          setError(response.data.message);
        }
      } else {
        // Otherwise, create a new photo
        const response = await api.post('/index.php?api=photo&action=create', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
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
          placeholder="Tags (comma separated)"
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
