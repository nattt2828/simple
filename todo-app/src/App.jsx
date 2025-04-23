import { useState, useEffect } from 'react';
import './index.css';

function TodoList() {
  const [tasks, setTasks] = useState([]);
  const [editingIndex, setEditingIndex] = useState(null);
  const [editText, setEditText] = useState('');
  const [filter, setFilter] = useState('All');
  const [darkMode, setDarkMode] = useState(() => {
    return localStorage.getItem('darkMode') === 'true';
  });

  // API base URL - adjust if needed
  const API_URL = 'http://localhost/todo-list/api';

  // Fetch todos from PHP API
  const fetchTasks = async () => {
    try {
      const response = await fetch(`${API_URL}/get-todos.php?filter=${filter}`);
      if (!response.ok) throw new Error('Failed to fetch tasks');
      const data = await response.json();
      setTasks(data);
    } catch (error) {
      console.error('Error fetching tasks:', error);
    }
  };

  useEffect(() => {
    fetchTasks();
  }, [filter]);

  useEffect(() => {
    document.documentElement.classList.toggle('dark-mode', darkMode);
    localStorage.setItem('darkMode', darkMode);
  }, [darkMode]);

  // Add new todo
  const addTask = async (text) => {
    try {
      const response = await fetch(`${API_URL}/add-todo.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ text, completed: false }),
      });
      
      if (!response.ok) throw new Error('Failed to add task');
      const newTask = await response.json();
      setTasks([...tasks, newTask]);
    } catch (error) {
      console.error('Error adding task:', error);
    }
  };

  // Toggle todo completion
  const toggleCompletion = async (id) => {
    try {
      const response = await fetch(`${API_URL}/toggle-todo.php`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id }),
      });
      
      if (!response.ok) throw new Error('Failed to toggle task');
      const updatedTask = await response.json();
      
      setTasks(tasks.map(task => 
        task.id === updatedTask.id 
          ? { ...task, completed: updatedTask.completed } 
          : task
      ));
    } catch (error) {
      console.error('Error toggling task:', error);
    }
  };

  // Start editing a task
  const startEditing = (id) => {
    const taskToEdit = tasks.find(task => task.id === id);
    setEditingIndex(id);
    setEditText(taskToEdit.text);
  };

  // Save edit
  const saveEdit = async (id) => {
    try {
      const response = await fetch(`${API_URL}/update-todo.php`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, text: editText }),
      });
      
      if (!response.ok) throw new Error('Failed to update task');
      
      setTasks(tasks.map(task => 
        task.id === id 
          ? { ...task, text: editText } 
          : task
      ));
      setEditingIndex(null);
    } catch (error) {
      console.error('Error updating task:', error);
    }
  };

  // Delete a task
  const deleteTask = async (id) => {
    try {
      const response = await fetch(`${API_URL}/delete-todo.php`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id }),
      });
      
      if (!response.ok) throw new Error('Failed to delete task');
      setTasks(tasks.filter(task => task.id !== id));
    } catch (error) {
      console.error('Error deleting task:', error);
    }
  };

  // Handle form submission
  const handleSubmit = (e) => {
    e.preventDefault();
    const input = e.target.elements.taskInput;
    const text = input.value.trim();
    if (text) {
      addTask(text);
      input.value = '';
    }
  };

  const filteredTasks = tasks;

  return (
    <div className="todo-container">
      <div className="header">
        <h1>Todo List</h1>
        <button 
          className="toggle-theme" 
          onClick={() => setDarkMode(!darkMode)}
        >
          {darkMode ? '☀️' : '🌙'}
        </button>
      </div>
      
      <form onSubmit={handleSubmit}>
        <input 
          type="text" 
          name="taskInput" 
          placeholder="Add a new task..." 
          className="task-input" 
        />
        <button type="submit" className="add-button">Add</button>
      </form>
      
      <div className="filters">
        <button 
          className={filter === 'All' ? 'active' : ''} 
          onClick={() => setFilter('All')}
        >
          All
        </button>
        <button 
          className={filter === 'Active' ? 'active' : ''} 
          onClick={() => setFilter('Active')}
        >
          Active
        </button>
        <button 
          className={filter === 'Completed' ? 'active' : ''} 
          onClick={() => setFilter('Completed')}
        >
          Completed
        </button>
      </div>
      
      <ul className="task-list">
        {filteredTasks.map(task => (
          <li key={task.id} className={task.completed ? 'completed' : ''}>
            {editingIndex === task.id ? (
              <div className="edit-container">
                <input 
                  type="text" 
                  value={editText} 
                  onChange={(e) => setEditText(e.target.value)} 
                  className="edit-input" 
                />
                <button onClick={() => saveEdit(task.id)} className="save-button">
                  Save
                </button>
              </div>
            ) : (
              <>
                <div className="task-content">
                  <input 
                    type="checkbox" 
                    checked={task.completed} 
                    onChange={() => toggleCompletion(task.id)} 
                  />
                  <span className="task-text">{task.text}</span>
                </div>
                <div className="task-actions">
                  <button onClick={() => startEditing(task.id)} className="edit-button">
                    Edit
                  </button>
                  <button onClick={() => deleteTask(task.id)} className="delete-button">
                    Delete
                  </button>
                </div>
              </>
            )}
          </li>
        ))}
      </ul>
    </div>
  );
}

export default TodoList;