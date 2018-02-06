$(function () {

    //Ajout de la classe active dans le menu de navigation pour la page en cours
    $('#add-route').addClass("active");

    // Ajout des sous formulaires de question et description
    var $container = $('div#add_mark_add_descriptions');
    var $containerQuestion = $('div#add_mark_add_questions');
    for (var i = 0; i < 2; i++) {
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, '<strong>Description n°' + (i + 1) + '</strong>')
            .replace(/__name__/g, i)
        ;
        var template2 = $containerQuestion.attr('data-prototype')
            .replace(/__name__label__/g, '<strong>Question n°' + (i + 1) + '</strong>')
            .replace(/__name__/g, i)
        ;
        var $prototype = $(template);
        var $prototype2 = $(template2);
        $container.append($prototype);
        $containerQuestion.append($prototype2);
    }

    /**
     * En cas de clique sur un repère deja existant
     */
    $('.tableAdd').click(function (e) {
        e.preventDefault();
        //Pour annuler le declenchement de l'event click sur la div map
        e.stopPropagation();
        var name = $(this).attr('name');
        $(this).children().css("background-color", "blue");
        addToCurrentRoute(name);
    });

    /**
     * Ajout d'un nouveau repère au clique sur la map (avec mise à jour des coordonnées dans le formulaire
     */
    $('#map').click(function (e) {
        //Recupere les coordonnées du clique par rapport a la div #map
        var coordX = e.pageX - $(this).offset().left;
        var coordY = e.pageY - $(this).offset().top;
        var mapWidth = $('#map').width();
        var mapHeight = $('#map').height();

        //Pour les afficher plus facilement par la suite on stock les coordonnées
        //en % de la hauteur et largeur
        coordX = (coordX / mapWidth).toFixed(3);
        coordY = (coordY / mapHeight).toFixed(3);

        //Affiche les coordonnées dans le formulaire
        $('#add_mark_add_coordinateX').val(coordX);
        $('#add_mark_add_coordinateY').val(coordY);

        //Créé une nouvelle div et l'affiche sur la map pour representer le nouvel ajout d'un repère
        var p = document.createElement("div");
        p.setAttribute("id", "repereMap");
        p.style.width = 10 + "px";
        p.style.height = 10 + "px";
        p.style.backgroundColor = "blue";
        //Sans oublier de convertir le % en valeur en pixel
        p.style.left = (coordX * mapWidth) + 'px';
        p.style.top = (coordY * mapHeight) + 'px';
        $('#map').append(p);
    });

    /**
     * Gestion de l'affichage de la liste des repères si un parcours pré-existant est selectionné
     */
    $('#form_route').change(function () {

        var name = $('#form_route option:selected').text();
        //On verifie d'abord si la selection n'est pas le placeholder
        if (name != 'Choisir le parcours à modifier') {
            //Affiche l'animation de chargment
            $('#animation').show();
            $('#table-mark').fadeOut('slow');
            //Récupère tous les repères liés au parcours
            $.ajax({
                url: '/ajax/getMarks',
                type: 'POST',
                data: {name: name}
            })
                .done(function (response) {
                    //Masque l'animation et affiche le resultat dans le tableau des repères
                    $('#animation').hide();
                    $('#table-mark').fadeIn('slow');
                    $('#table-mark > tbody:last').html(response);
                    //Modification des valeurs dans les infos générales du parcours
                    $('#name').val($('#route_name').val());
                    $('#description').val($('#route_description').val());
                    $('#hours').val($('#route_hours').val());
                    $('#minutes').val($('#route_minutes').val() * 1);

                    // A cause de l'ajout de HTML il faut re-inserer les event listener
                    $('.deleteMark').click(function (e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        $(this).parent().parent().remove();
                        removeMark(name);
                    });
                    $('.editMark').click(function (e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        editMark(name);

                    });
                    //Enfin on modifie le background de pour visualiser les repères deja présent dans le parcours
                    $.each($('.coordinates'), function () {
                        //Recupere les coordonnées dans les data du tableau
                        var name = encodeURI($(this).text());
                        $("#repereMap[name='" + name + "']")[0].style.backgroundColor = "blue";
                    });
                });
        }

    });

    /**
     * Ajout d'un repère au parcours
     */
    $('#add_mark_add_save').click(function (e) {
        e.preventDefault();
        addMarkToBdd();

        //Même chose il faut rajouter les event listener du fait qu'on insere du HTML
        $('.editMark').click(function (e) {
            e.preventDefault();
            var name = $(this).attr('name');
            editMark(name);

        });

        $('.deleteMark').click(function (e) {
            e.preventDefault();
            var name = $(this).attr('name');
            $(this).parent().parent().remove();
            removeMark(name);
        });
    });

    /**
     *  Ajout du parcours en BDD
     */

    $('#submit-info-parcours').click(function (e) {
        e.preventDefault();
        var tabError = [];
        if($('#name').val() === "")
        {
            tabError.push('Veuillez entrer un nom de parcours');

        }
        if($('#description').val() === "")
        {
            tabError.push('Veuillez entrer une description pour le parcours');
        }
        if(($('#hours').val() === "") || ($('#minutes').val() === ""))
        {
            tabError.push("Les heures ou minutes de la durée ne peuvent pas être nulles");
        }
        if(tabError.length !== 0)
        {
            var message = "";
            for(var i = 0 ; i<tabError.length; i++)
            {
                message += tabError[i] + "\n";
            }
            alert(message);
        }
        else {
            //Serialize les données du formulaire
            var $routeInfo = $('#add_route').serialize();
            //Récupere le précedent pour pouvoir tester si il c'est un update d'un parcours existant en PHP
            var name = $('#form_route option:selected').text();
            $.ajax({
                url: '/ajax/saveRoutetoBDD',
                type: 'POST',
                data: {routeInfo: $routeInfo, name: name}
            })
                .done(function () {
                    //Recharge la page en cas de succès pour la mise à jour du select des parcours
                    document.location.reload(true);
                })
                .fail(function () {
                    // Petit clin d'oeil en cas d'échec
                    var player = document.querySelector('#audioPlayer');
                    player.play();
                    alert("AH!");

                });
        }
    });

    /**
     * Suppression d'une ligne du tableau et de sa correspondance dans le tableau de nom en session
     */
    function removeMark(name) {
        //Pour la recherche dans le tableau de repère en session il faut le nom originel du repère
        var decodedName = decodeURI(name);
        $.ajax({
            url: '/ajax/deleteMarkFromSession',
            type: 'POST',
            data: {name: decodedName}
        });
        //On change le bg-color pour signaler à l'utilisteur que ce repère n'est plus présent dans le parcours actuel
        $("#repereMap[name='" + name + "']")[0].style.backgroundColor = "red";
    }

    /**
     * Modification d'un repère ajouté
     */
    function editMark(name) {
        var decodedName = decodeURI(name);
        //Dans le but d'un recherche en BDD il faut garder l'ancien nom du repère en memoire il est donc stocké dans un input caché
        $("#previousName").val(decodedName);

        $.ajax({
            url: '/ajax/getMarkInfo',
            type: 'POST',
            data: {name: decodedName}
        })
            .done(function (response) {

                //Remplissage manuel des differents champsdu formulaire sur les 3 onglets
                $('#add_mark_add_name').val(response.name);
                $('#add_mark_add_coordinateX').val(response.coordinateX);
                $('#add_mark_add_coordinateY').val(response.coordinateY);
                $('#add_mark_add_medias').val(response.medias);

                $('#add_mark_add_descriptions_0_label').val(response.description1.label);
                $('#add_mark_add_descriptions_0_category').val(response.description1.category);
                $('#add_mark_add_descriptions_1_label').val(response.description2.label);
                $('#add_mark_add_descriptions_1_category').val(response.description2.category);

                $('#add_mark_add_questions_0_label').val(response.question1.label);
                $('#add_mark_add_questions_0_category').val(response.question1.category);
                $('#add_mark_add_questions_0_answers_goodAnswer').val(response.question1.answers.goodAnswer);
                $('#add_mark_add_questions_0_answers_answer1').val(response.question1.answers.answer1);
                $('#add_mark_add_questions_0_answers_answer2').val(response.question1.answers.answer2);
                $('#add_mark_add_questions_0_answers_answer3').val(response.question1.answers.answer3);

                $('#add_mark_add_questions_1_label').val(response.question2.label);
                $('#add_mark_add_questions_1_category').val(response.question2.category);
                $('#add_mark_add_questions_1_answers_goodAnswer').val(response.question2.answers.goodAnswer);
                $('#add_mark_add_questions_1_answers_answer1').val(response.question2.answers.answer1);
                $('#add_mark_add_questions_1_answers_answer2').val(response.question2.answers.answer2);
                $('#add_mark_add_questions_1_answers_answer3').val(response.question2.answers.answer3);

            });
    }

    /**
     * Ajout ou modification d'un repère en BDD
     */
    function addMarkToBdd() {

        /**
         * Dans un premier temps il faut tester que tout les champs du formulaires soient remplis comme on l'attend
         *      - aucun champs vide
         *      - une description et une question par categorie
         *      - des coordonnées numeriques
         */
        var tabError = [];
        if($('#add_mark_add_name').val() ==="")
        {
            tabError.push('Veuillez entrer un nom');

        }
        if($('#add_mark_add_coordinateX').val() === "")
        {
            tabError.push('Veuillez entrer une coordonnées en X');

        }
        if($('#add_mark_add_coordinateY').val() === "")
        {
            tabError.push('Veuillez entrer une coordonnées en Y');
        }
        if(($('#add_mark_add_descriptions_0_label').val() === "") || ($('#add_mark_add_descriptions_1_label').val() === ""))
        {
            tabError.push("Le champ de description ne peut pas être vide");
        }
        if(($('#add_mark_add_questions_0_label').val() === "") || ($('#add_mark_add_questions_1_label').val() === ""))
        {
            tabError.push("Le champ de question ne peut pas être vide");
        }
        if(($('#add_mark_add_questions_0_answers_goodAnswer').val() === "") || ($('#add_mark_add_questions_1_answers_goodAnswer').val() === ""))
        {
            tabError.push("Veuillez renseigner une bonne réponse pour chaque question");
        }
        if(($('#add_mark_add_questions_0_answers_answer1').val() === "") ||
            ($('#add_mark_add_questions_0_answers_answer2').val() === "") ||
            ($('#add_mark_add_questions_0_answers_answer3').val() === "") ||
            ($('#add_mark_add_questions_1_answers_answer1').val() === "") ||
            ($('#add_mark_add_questions_1_answers_answer2').val() === "") ||
            ($('#add_mark_add_questions_1_answers_answer3').val() === ""))
        {
            tabError.push("Veuillez renseigner chaque réponse possible pour les 2 questions");
        }
        if(tabError.length !== 0)
        {
            var message = "";
            for(var i = 0 ; i<tabError.length; i++)
            {
                message += tabError[i] + "\n";
            }
            alert(message);
        }
        else
        {
            //Serialize le formulaire d'ajout de repère
            var $markInfo = $('[name =add_mark_add]').serialize();
            //On recupere la valeur de l'input caché qui determine si on a affaire
            // à une modification ou un nouvel ajout
            var previousName = $("#previousName").val();

            //Dans le but de le comparer à un nom encodé il faut encoder cette variable
            var previousNameEncoded = encodeURI(previousName);

            //On affiche la div permettant de desactiver la saisie sur le formulaire
            //Elle reste active 3s avant de fadeOut
            $("#hideForm").css('z-index', 3000);
            $("#hideForm").show();
            $("#hideForm").delay(3000).fadeOut(800);

            $.ajax({
                url: '/ajax/saveMarkToSession',
                type: 'POST',
                data: {markInfo: $markInfo, update: previousName}
            }).done(function () {

                //En cas de réussite on affiche le message de succès
                $("#validationMessage").css('z-index', 3001);
                $("#validationMessage").show();
                $("#validationMessage").delay(3000).fadeOut(800);

                //Puis on "recache" les divs
                setTimeout(function () {
                    $("#hideForm").css('z-index', -1);
                    $("#validationMessage").css('z-index', -1);
                }, 3800);


                //On recupere le nom du nouveau repère pour le stocker dans le tableau de repères
                var newName = $('#add_mark_add_name').val();

                //Pour pouvoir ajouter ce nom dans un attribut [name] il faut encoder le nouveau nom pour convertir
                // les espaces et caractères spéciaux
                var encodedNewName = encodeURI(newName);

                $('[name =add_mark_add]')[0].reset();

                //Si previousName vaut false il s'agit d'un nouvel ajout et non d'un update donc il faut créer une lgine dans le tableau
                if (previousNameEncoded == 'false') {
                    $("#previousName").val('false');
                    //Recupère le nombre de ligne actuel pour numeroter la nouvelle insertion
                    var nbreRows = $('#table-mark tbody tr').length;
                    nbreRows++;
                    var newRow = "<tr>\n" +
                        "        <th scope=\"row\">" + nbreRows + "</th>\n" +
                        "        <td name=\"" + encodedNewName + "\">" + newName + "</td>\n" +
                        "        <td><a href=\"#\" name=\"" + encodedNewName + "\" class=\"editMark\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a></td>\n" +
                        "        <td><a href=\"#\" name=\"" + encodedNewName + "\" class=\"deleteMark\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a></td>\n" +
                        "    </tr>";
                    //Enfin on ajoute la nouvelle ligne au tableau
                    $('#table-mark > tbody:last').append(newRow);


                    // Encore une fois à cause de l'ajout de HTML il faut remettre les event listener
                    $('.editMark').click(function (e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        editMark(name);

                    });

                    $('.deleteMark').click(function (e) {
                        e.preventDefault();
                        var name = $(this).attr('name');
                        $(this).parent().parent().remove();
                        removeMark(name);
                    });
                }
                //Sinon on met à jour la ligne du tableau deja existante en changeant également les [name] dans les liens
                else {
                    $("#table-mark td[name='" + previousNameEncoded + "']").html(newName).attr('name', encodedNewName);
                    $("#table-mark a[name='" + previousNameEncoded + "']").each(function () {
                        $(this).attr('name', encodedNewName);
                    });

                }
                //En cas d'échec de la requète AJAX on affiche un message d'erreur
            }).fail(function () {
                $("#errorMessage").css('z-index', 3001);
                $("#errorMessage").show();
                $("#errorMessage").delay(3000).fadeOut(800);
                setTimeout(function () {
                    $("#hideForm").css('z-index', -1);
                    $("#errorMessage").css('z-index', -1);
                }, 3800);
            })
        }



    }

    /**
     * Au clic sur un repere preexistant l'ajoute au parcours en cours
     */
    function addToCurrentRoute(name) {
        var newMarkName = decodeURI(name);
        // On commence par l'ajouter dans le tableau de session regroupant les noms des repères présents dans le parcours
        $.ajax({
            url: '/ajax/addToSession',
            type: 'POST',
            data: {newMarkName: newMarkName}
        }).done(function () {
            //Si il n'y a pas eu de probleme pendant la requète AJAX on ajout le repère au tableau HTML
            var nbreRows = $('#table-mark tbody tr').length;
            nbreRows++;
            var newRow = "<tr>\n" +
                "        <th scope=\"row\">" + nbreRows + "</th>\n" +
                "        <td name=\"" + name + "\">" + newMarkName + "</td>\n" +
                "        <td><a href=\"#\" name=\"" + name + "\" class=\"editMark\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></a></td>\n" +
                "        <td><a href=\"#\" name=\"" + name + "\" class=\"deleteMark\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></a></td>\n" +
                "    </tr>";
            $('#table-mark > tbody:last').append(newRow);

            // Et on refresh les event listener
            $('.editMark').click(function (e) {
                e.preventDefault();
                var name = $(this).attr('name');
                editMark(name);

            });

            $('.deleteMark').click(function (e) {
                e.preventDefault();
                var name = $(this).attr('name');
                $(this).parent().parent().remove();
                removeMark(name);
            });
        });

    }

});
