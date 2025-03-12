import React from 'react';
import './Modal.css';

const Modal = ({ children, onClose }) => {
  const handleOverlayClick = (e) => {
    // If the user clicks outside the modal content, close it
    if (e.target.classList.contains('modal-overlay')) {
      onClose();
    }
  };

  return (
    <div className="modal-overlay" onClick={handleOverlayClick}>
      <div className="modal-content">
        {children}
      </div>
    </div>
  );
};

export default Modal;
