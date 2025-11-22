<?php
session_start();
include('verificar_admin.php');
include('conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Atividades</title>
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body>

    <header>
        <nav class="nav-bar">
            <div class="logo">
                <h1>Monitoramento de Acessos</h1>
            </div>
            <div class="nav-list">
                <ul>
                    <li class="nav-item"><a href="admin.php" class="nav-link">Voltar ao Painel</a></li>
                    <li class="nav-item"><a href="logout.php" class="nav-link">Sair</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="admin-container" style="max-width: 1000px;">
            
            <div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <h2>Histórico de Entradas e Saídas</h2>
                
                <button onclick="gerarPDF()" class="btn-admin" style="background-color: #dc3545; color: white; display: flex; align-items: center; gap: 5px;">
                    Baixar Relatório em PDF
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table class="tabela-produtos" id="tabelaLogs">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Login</th>
                            <th>Ação</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT l.*, u.nome, u.login 
                                FROM logs_atividades l 
                                JOIN usuarios u ON l.usuario_id = u.id 
                                ORDER BY l.data_hora DESC 
                                LIMIT 200";
                        
                        $result = $conn->query($sql);
                        
                        if ($result && $result->num_rows > 0):
                            while($log = $result->fetch_assoc()):
                                $data_formatada = date('d/m/Y H:i:s', strtotime($log['data_hora']));
                                $cor_acao = ($log['acao'] == 'Logout') ? '#dc3545' : '#28a745';
                        ?>
                            <tr>
                                <td><?php echo $data_formatada; ?></td>
                                <td><?php echo htmlspecialchars($log['nome']); ?></td>
                                <td><?php echo htmlspecialchars($log['login']); ?></td>
                                <td style="font-weight: bold; color: <?php echo $cor_acao; ?>;">
                                    <?php echo htmlspecialchars($log['acao']); ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($log['ip']); ?>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Nenhum registro encontrado.</td>
                            </tr>
                        <?php
                        endif;
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        function gerarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.setFontSize(18);
            doc.text("Relatório de Acessos - Toca das Bebidas", 14, 20);
            
            doc.setFontSize(10);
            doc.text("Gerado em: " + new Date().toLocaleString(), 14, 28);

            doc.autoTable({
                html: '#tabelaLogs',
                startY: 35,
                theme: 'grid',
                headStyles: { fillColor: [24, 95, 100] },
                styles: { fontSize: 9 },
                columnStyles: {
                    0: { cellWidth: 40 },
                    3: { fontStyle: 'bold' }
                },
                didParseCell: function(data) {
                    if (data.section === 'body' && data.column.index === 3) {
                        if (data.cell.raw.innerText.includes('Logout')) {
                            data.cell.styles.textColor = [220, 53, 69];
                        } else {
                            data.cell.styles.textColor = [40, 167, 69];
                        }
                    }
                }
            });

            doc.save('Relatorio_Logs_Acessos.pdf');
        }
    </script>
</body>
</html>