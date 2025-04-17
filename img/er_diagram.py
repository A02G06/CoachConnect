from graphviz import Digraph

# Create ER Diagram
er = Digraph('ER Diagram', filename='er_diagram', format='png')

# Entities
er.node('Student', shape='rectangle')
er.node('Coach', shape='rectangle')
er.node('Session', shape='rectangle')
er.node('Payment', shape='rectangle')
er.node('Subject', shape='rectangle')
er.node('Review', shape='rectangle')

# Relationships
er.edge('Student', 'Session', label='Books')
er.edge('Coach', 'Session', label='Conducts')
er.edge('Session', 'Payment', label='Has')
er.edge('Subject', 'Session', label='Covers')
er.edge('Student', 'Review', label='Writes')
er.edge('Coach', 'Review', label='Receives')

# Generate diagram
er_path = "/mnt/data/coach_connect_er_diagram.png"
er.render(er_path, format='png', cleanup=True)

# Return file path
er_path
