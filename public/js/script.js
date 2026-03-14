/**
 * JavaScript Principal do Sistema
 * Pet Shop SaaS
 */

// Ocultar parâmetros de filtro da URL em páginas de listagem
(function() {
    var params = new URLSearchParams(window.location.search);
    var page   = params.get('page');
    var action = params.get('action');
    if (page && action === 'list' && params.toString() !== 'page=' + page + '&action=list') {
        history.replaceState(null, '', window.location.pathname + '?page=' + page + '&action=list');
    }
})();

$(document).ready(function() {

    /* ------------------------------------------------
       SIDEBAR RESPONSIVO
       ------------------------------------------------ */
    var $sidebar  = $('.sidebar');
    var $overlay  = $('.sidebar-overlay');
    var $toggle   = $('.btn-toggle-sidebar');
    var BREAKPOINT = 1024;

    function isMobile() {
        return window.innerWidth < BREAKPOINT;
    }

    function openSidebar() {
        $sidebar.addClass('open');
        $overlay.addClass('active');
        $('body').css('overflow', 'hidden');
        $toggle.attr('aria-expanded', 'true');
    }

    function closeSidebar() {
        $sidebar.removeClass('open');
        $overlay.removeClass('active');
        $('body').css('overflow', '');
        $toggle.attr('aria-expanded', 'false');
    }

    // Botão hamburguer
    $toggle.on('click', function() {
        $sidebar.hasClass('open') ? closeSidebar() : openSidebar();
    });

    // Botão X dentro do sidebar
    $('.sidebar-close').on('click', function() {
        closeSidebar();
    });

    // Clique no overlay fecha o sidebar
    $overlay.on('click', function() {
        closeSidebar();
    });

    // Tecla ESC fecha o sidebar
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $sidebar.hasClass('open')) {
            closeSidebar();
        }
    });

    // Clique em link do menu fecha no mobile
    $('.menu ul li a').on('click', function() {
        if (isMobile()) {
            closeSidebar();
        }
    });

    // Ajusta ao redimensionar a janela
    var resizeTimer;
    $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (!isMobile()) {
                closeSidebar();
            }
        }, 100);
    });




    // Máscaras de entrada
    $('.phone-mask').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            if (value.length < 14) {
                value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            }
        }
        $(this).val(value);
    });

    $('.cpf-mask').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
        $(this).val(value);
    });

    $('.cep-mask').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        value = value.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
        $(this).val(value);
    });

    $('.money-mask').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        value = (parseInt(value) / 100).toFixed(2);
        value = value.replace('.', ',');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        $(this).val('R$ ' + value);
    });

    // Busca de CEP
    $('#cep').on('blur', function() {
        let cep = $(this).val().replace(/\D/g, '');

        if (cep.length === 8) {
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (!data.erro) {
                        $('#endereco').val(data.logradouro);
                        $('#cidade').val(data.localidade);
                        $('#estado').val(data.uf);
                    }
                }
            });
        }
    });

    // Confirmação de exclusão
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Tem certeza que deseja excluir este registro?')) {
            e.preventDefault();
            return false;
        }
    });

    // Auto-hide de alertas
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Busca de tutor (autocomplete)
    $('#buscar_tutor').on('input', function() {
        let termo = $(this).val();

        if (termo.length >= 3) {
            $.ajax({
                url: 'tutores.php?action=buscar',
                type: 'GET',
                data: { termo: termo },
                dataType: 'json',
                success: function(data) {
                    let results = $('#resultados_tutor');
                    results.empty();

                    if (data.length > 0) {
                        data.forEach(function(tutor) {
                            results.append(`
                                <div class="result-item" data-id="${tutor.id}" data-nome="${tutor.nome}">
                                    ${tutor.nome} - ${tutor.telefone || ''}
                                </div>
                            `);
                        });
                        results.show();
                    }
                }
            });
        }
    });

    // Selecionar tutor do autocomplete
    $(document).on('click', '.result-item', function() {
        let id = $(this).data('id');
        let nome = $(this).data('nome');

        $('#tutor_id').val(id);
        $('#buscar_tutor').val(nome);
        $('#resultados_tutor').hide();

        // Carregar pets do tutor
        if (typeof carregarPetsPorTutor === 'function') {
            carregarPetsPorTutor(id);
        }
    });

    // Busca de produto (autocomplete)
    $('#buscar_produto').on('input', function() {
        let termo = $(this).val();

        if (termo.length >= 3) {
            $.ajax({
                url: 'produtos.php?action=buscar',
                type: 'GET',
                data: { termo: termo },
                dataType: 'json',
                success: function(data) {
                    let results = $('#resultados_produto');
                    results.empty();

                    if (data.length > 0) {
                        data.forEach(function(produto) {
                            results.append(`
                                <div class="result-item"
                                     data-id="${produto.id}"
                                     data-nome="${produto.nome}"
                                     data-preco="${produto.preco_venda}"
                                     data-estoque="${produto.estoque_atual}">
                                    ${produto.nome} - R$ ${produto.preco_venda} (Estoque: ${produto.estoque_atual})
                                </div>
                            `);
                        });
                        results.show();
                    }
                }
            });
        }
    });

    // Calcular valor total em formulários
    $('.calcular-total').on('input', function() {
        calcularTotal();
    });

    function calcularTotal() {
        let subtotal = 0;

        $('.item-preco').each(function() {
            let preco = parseFloat($(this).data('preco')) || 0;
            let quantidade = parseInt($(this).closest('tr').find('.item-quantidade').val()) || 1;
            subtotal += preco * quantidade;
        });

        let desconto = parseFloat($('#desconto').val()) || 0;
        let total = subtotal - desconto;

        $('#subtotal').text('R$ ' + subtotal.toFixed(2).replace('.', ','));
        $('#total').text('R$ ' + total.toFixed(2).replace('.', ','));
        $('#valor_total').val(total.toFixed(2));
    }

    // Validação de formulários
    $('form').on('submit', function(e) {
        let isValid = true;

        $(this).find('[required]').each(function() {
            if ($(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });

    // Imprimir
    $('.btn-print').on('click', function() {
        window.print();
    });

    // Exportar para Excel
    $('.btn-export-excel').on('click', function() {
        let table = $(this).data('table');
        window.location.href = 'export.php?table=' + table + '&format=excel';
    });

    // Filtros dinâmicos
    $('.filtro-dinamico').on('change', function() {
        $(this).closest('form').submit();
    });

    // Toggle de visualização
    $('.toggle-view').on('click', function(e) {
        e.preventDefault();
        let target = $(this).data('target');
        $(target).slideToggle();
    });

    // Copiar para área de transferência
    $('.btn-copy').on('click', function() {
        let text = $(this).data('copy');
        navigator.clipboard.writeText(text).then(function() {
            alert('Copiado para a área de transferência!');
        });
    });

});

// Funções auxiliares globais
function formatarMoeda(valor) {
    return 'R$ ' + parseFloat(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function formatarData(data) {
    if (!data) return '';
    let partes = data.split('-');
    return partes[2] + '/' + partes[1] + '/' + partes[0];
}

function confirmarExclusao(mensagem = 'Tem certeza que deseja excluir este registro?') {
    return confirm(mensagem);
}

function mostrarLoading() {
    $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
}

function esconderLoading() {
    $('.loading-overlay').remove();
}

function mostrarMensagem(tipo, mensagem) {
    let classe = tipo === 'success' ? 'alert-success' : 'alert-error';
    let alerta = `<div class="alert ${classe}">${mensagem}</div>`;
    $('.main-content').prepend(alerta);

    setTimeout(function() {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}
