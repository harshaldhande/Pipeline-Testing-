pipeline {
    agent any

    environment {
        IMAGE_NAME = "dockerforlearning0213/todo-app"
    }

    stages {

        stage('Checkout') {
            steps {
                echo "Checking out source code..."
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                sh '''
                    echo "========== Building Docker Image =========="

                    docker build -t $IMAGE_NAME:latest .

                    docker images | grep todo-app
                '''
            }
        }

        stage('Push Docker Image') {
            steps {

                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub',
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_PASS'
                    )
                ]) {

                    sh '''
                        echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin

                        docker push $IMAGE_NAME:latest

                        docker logout
                    '''
                }

            }
        }

        stage('Deploy to Kubernetes') {

            steps {

                withCredentials([
                    file(
                        credentialsId: 'kubeconfig',
                        variable: 'KUBECONFIG'
                    )
                ]) {

                    sh '''
                        echo "========== Restart Deployment =========="

                        kubectl rollout restart deployment/todo-app -n todo-app

                        echo ""

                        echo "========== Waiting for Rollout =========="

                        kubectl rollout status deployment/todo-app -n todo-app

                        echo ""

                        echo "========== Current Pods =========="

                        kubectl get pods -n todo-app -o wide

                        echo ""

                        echo "========== Deployment =========="

                        kubectl get deployment -n todo-app

                        echo ""

                        echo "========== Service =========="

                        kubectl get svc -n todo-app
                    '''

                }

            }

        }

    }

    post {

        success {
            echo "Deployment Successful"
        }

        failure {
            echo "Deployment Failed"
        }

        always {
            echo "Pipeline Finished"
        }

    }

}
