class MapGraph:
    def __init__(self):
        self.graph = {
            'toilet1': ['room1'],
            'room1': ['room2', 'room3'],
            'room2': ['room4'],
            'room3': ['room4'],
            'room4': []
        }
        self.instructions = {
            ('toilet1','room1'): 'Turn right 90 degrees, walk straight',
            ('room1','room2'): 'Turn left 90 degrees, walk straight',
            ('room1','room3'): 'Walk straight',
            ('room2','room4'): 'Turn right 90 degrees, walk straight',
            ('room3','room4'): 'Turn left 90 degrees, walk straight'
        }

    def get_route(self, start, end):
        # Simple BFS for shortest path
        from collections import deque
        queue = deque([[start]])
        visited = set()
        while queue:
            path = queue.popleft()
            node = path[-1]
            if node == end:
                return path
            if node not in visited:
                visited.add(node)
                for neighbor in self.graph.get(node, []):
                    new_path = list(path)
                    new_path.append(neighbor)
                    queue.append(new_path)
        return []

    def get_instruction(self, current, next_node):
        return self.instructions.get((current,next_node), "Walk straight")
