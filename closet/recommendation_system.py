import sys
import json
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import linear_kernel

def get_recommendations(item_id, items_json):
    """
    Gera recomendações de itens com base na descrição de um item.
    Utiliza um sistema de recomendação baseado em conteúdo (Content-Based Filtering)
    com similaridade de cosseno em vetores TF-IDF.
    """
    try:
        # 1. Carregar dados
        items = json.loads(items_json)
        df = pd.DataFrame(items)
        
        # Garantir que a coluna 'description' não tenha valores nulos
        df['description'] = df['description'].fillna('')
        
        # 2. Criar matriz TF-IDF
        tfidf = TfidfVectorizer(stop_words='english')
        tfidf_matrix = tfidf.fit_transform(df['description'])
        
        # 3. Calcular similaridade de cosseno
        cosine_sim = linear_kernel(tfidf_matrix, tfidf_matrix)
        
        # 4. Mapear o ID do item para o índice do DataFrame
        # Cria uma Series onde o índice é o ID do item e o valor é o índice do DataFrame
        indices = pd.Series(df.index, index=df['id']).drop_duplicates()
        
        # 5. Obter o índice do item de referência
        if item_id not in indices:
            return json.dumps({"error": "Item de referência não encontrado no conjunto de dados."})

        idx = indices[item_id]
        
        # 6. Obter as pontuações de similaridade do item de referência com todos os outros itens
        sim_scores = list(enumerate(cosine_sim[idx]))
        
        # 7. Ordenar os itens com base nas pontuações de similaridade
        sim_scores = sorted(sim_scores, key=lambda x: x[1], reverse=True)
        
        # 8. Obter os índices dos 10 itens mais similares (excluindo o próprio item)
        # Pega os 10 primeiros, pulando o primeiro (que é o próprio item)
        sim_scores = sim_scores[1:11]
        item_indices = [i[0] for i in sim_scores]
        
        # 9. Retornar os IDs dos itens recomendados
        recommended_items = df['id'].iloc[item_indices].tolist()
        
        return json.dumps({"recommendations": recommended_items})

    except Exception as e:
        return json.dumps({"error": f"Erro ao gerar recomendações: {str(e)}"})

if __name__ == "__main__":
    # O primeiro argumento é o nome do script, o segundo é o item_id, o terceiro é o items_json
    if len(sys.argv) != 3:
        print(json.dumps({"error": "Uso: python3 recommendation_system.py <item_id> <items_json>"}))
        sys.exit(1)

    item_id = int(sys.argv[1])
    items_json = sys.argv[2]
    
    # O JSON passado como argumento de linha de comando pode precisar de decodificação de URL
    # ou tratamento de aspas, mas o Laravel já deve passar como string.
    try:
        from urllib.parse import unquote
        items_json = unquote(items_json)
    except ImportError:
        pass # Não é necessário em todos os ambientes Python

    recommendations = get_recommendations(item_id, items_json)
    print(recommendations)
